<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\View\Result;

use Magento\Framework;
use Magento\Framework\View;
use Magento\Framework\App\ResponseInterface;

/**
 * A "page" result that encapsulates page type, page configuration
 * and imposes certain layout handles.
 *
 * The framework convention is that there will be loaded a guaranteed handle for "all pages",
 * then guaranteed handle that corresponds to page type
 * and a guaranteed handle that stands for page layout (a wireframe of a page)
 *
 * Page result is a more specific implementation of a generic layout response
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Page extends Layout
{
    /**
     * @var string
     */
    protected $pageLayout;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * @var \Magento\Framework\View\Page\Config\Renderer
     */
    protected $pageConfigRenderer;

    /**
     * @var \Magento\Framework\View\Page\Config\RendererFactory
     */
    protected $pageConfigRendererFactory;

    /**
     * @var \Magento\Framework\View\Page\Layout\Reader
     */
    protected $pageLayoutReader;

    /**
     * @var \Magento\Framework\View\FileSystem
     */
    protected $viewFileSystem;

    /**
     * @var array
     */
    protected $viewVars;

    /**
     * @var string
     */
    protected $template;

    /**
     * @var Framework\App\RequestInterface
     */
    protected $request;

    /**
     * Asset service
     *
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;

    /**
     * @var Framework\Logger
     */
    protected $logger;

    /**
     * @var Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * Constructor
     *
     * @param View\Element\Template\Context $context
     * @param View\LayoutFactory $layoutFactory
     * @param View\Layout\Reader\Pool $layoutReaderPool
     * @param Framework\Translate\InlineInterface $translateInline
     * @param View\Layout\BuilderFactory $layoutBuilderFactory
     * @param View\Page\Config\RendererFactory $pageConfigRendererFactory
     * @param View\Page\Layout\Reader $pageLayoutReader
     * @param string $template
     * @param bool $isIsolated
     */
    public function __construct(
        View\Element\Template\Context $context,
        View\LayoutFactory $layoutFactory,
        View\Layout\Reader\Pool $layoutReaderPool,
        Framework\Translate\InlineInterface $translateInline,
        View\Layout\BuilderFactory $layoutBuilderFactory,
        View\Page\Config\RendererFactory $pageConfigRendererFactory,
        View\Page\Layout\Reader $pageLayoutReader,
        $template,
        $isIsolated = false
    ) {
        $this->request = $context->getRequest();
        $this->assetRepo = $context->getAssetRepository();
        $this->logger = $context->getLogger();
        $this->urlBuilder = $context->getUrlBuilder();
        $this->pageConfig = $context->getPageConfig();
        $this->pageLayoutReader = $pageLayoutReader;
        $this->viewFileSystem = $context->getViewFileSystem();
        $this->pageConfigRendererFactory = $pageConfigRendererFactory;
        $this->template = $template;
        parent::__construct(
            $context,
            $layoutFactory,
            $layoutReaderPool,
            $translateInline,
            $layoutBuilderFactory,
            $isIsolated
        );
        $this->initPageConfigReader();
    }

    /**
     * Initialize page config reader
     *
     * @return void
     */
    protected function initPageConfigReader()
    {
        $this->pageConfigRenderer = $this->pageConfigRendererFactory->create(['pageConfig' => $this->pageConfig]);
    }

    /**
     * Create layout builder
     *
     * @return void
     */
    protected function initLayoutBuilder()
    {
        $this->layoutBuilderFactory->create(View\Layout\BuilderFactory::TYPE_PAGE, [
            'layout' => $this->layout,
            'pageConfig' => $this->pageConfig,
            'pageLayoutReader' => $this->pageLayoutReader
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function initLayout()
    {
        $this->addHandle('default');
        $this->addHandle($this->getDefaultLayoutHandle());
        $update = $this->getLayout()->getUpdate();
        if ($update->isLayoutDefined()) {
            $update->removeHandle('default');
        }
        return parent::initLayout();
    }

    /**
     * Add default handle
     *
     * @return $this
     */
    public function addDefaultHandle()
    {
        $this->addHandle('default');
        return parent::addDefaultHandle();
    }

    /**
     * @return \Magento\Framework\View\Page\Config
     */
    public function getConfig()
    {
        return $this->pageConfig;
    }

    /**
     * Add layout updates handles associated with the action page
     *
     * @param array|null $parameters page parameters
     * @param string|null $defaultHandle
     * @return bool
     */
    public function addPageLayoutHandles(array $parameters = array(), $defaultHandle = null)
    {
        $handle = $defaultHandle ? $defaultHandle : $this->getDefaultLayoutHandle();
        $pageHandles = array($handle);
        foreach ($parameters as $key => $value) {
            $pageHandles[] = $handle . '_' . $key . '_' . $value;
        }
        // Do not sort array going into add page handles. Ensure default layout handle is added first.
        return $this->addHandle($pageHandles);
    }

    /**
     * @param ResponseInterface $response
     * @return $this
     */
    protected function render(ResponseInterface $response)
    {
        $this->pageConfig->publicBuild();
        if ($this->getPageLayout()) {
            $config = $this->getConfig();
            $this->addDefaultBodyClasses();
            $addBlock = $this->getLayout()->getBlock('head.additional'); // todo
            $requireJs = $this->getLayout()->getBlock('require.js');
            $this->assign([
                'requireJs' => $requireJs ? $requireJs->toHtml() : null,
                'headContent' => $this->pageConfigRenderer->renderHeadContent(),
                'headAdditional' => $addBlock ? $addBlock->toHtml() : null,
                'htmlAttributes' => $this->pageConfigRenderer->renderElementAttributes($config::ELEMENT_TYPE_HTML),
                'headAttributes' => $this->pageConfigRenderer->renderElementAttributes($config::ELEMENT_TYPE_HEAD),
                'bodyAttributes' => $this->pageConfigRenderer->renderElementAttributes($config::ELEMENT_TYPE_BODY),
                'loaderIcon' => $this->getViewFileUrl('images/loader-2.gif')
            ]);

            $output = $this->getLayout()->getOutput();
            $this->translateInline->processResponseBody($output);
            $this->assign('layoutContent', $output);
            $response->appendBody($this->renderPage());
        } else {
            parent::render($response);
        }
        return $this;
    }

    /**
     * Add default body classes for current page layout
     *
     * @return $this
     */
    protected function addDefaultBodyClasses()
    {
        $this->pageConfig->addBodyClass($this->request->getFullActionName('-'));
        $pageLayout = $this->getPageLayout();
        if ($pageLayout) {
            $this->pageConfig->addBodyClass('page-layout-' . $pageLayout);
        }
        return $this;
    }

    /**
     * @return string
     */
    protected function getPageLayout()
    {
        return $this->pageConfig->getPageLayout() ?: $this->getLayout()->getUpdate()->getPageLayout();
    }

    /**
     * Assign variable
     *
     * @param   string|array $key
     * @param   mixed $value
     * @return  $this
     */
    public function assign($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $subKey => $subValue) {
                $this->assign($subKey, $subValue);
            }
        } else {
            $this->viewVars[$key] = $value;
        }
        return $this;
    }

    /**
     * Render page template
     *
     * @return string
     * @throws \Exception
     */
    protected function renderPage()
    {
        $fileName = $this->viewFileSystem->getTemplateFileName($this->template);
        if (!$fileName) {
            throw new \InvalidArgumentException('Template "' . $this->template . '" is not found');
        }

        ob_start();
        try {
            extract($this->viewVars, EXTR_SKIP);
            include $fileName;
        } catch (\Exception $exception) {
            ob_end_clean();
            throw $exception;
        }
        $output = ob_get_clean();
        return $output;
    }

    /**
     * Retrieve url of a view file
     *
     * @param string $fileId
     * @param array $params
     * @return string
     */
    protected function getViewFileUrl($fileId, array $params = array())
    {
        try {
            $params = array_merge(array('_secure' => $this->request->isSecure()), $params);
            return $this->assetRepo->getUrlWithParams($fileId, $params);
        } catch (\Magento\Framework\Exception $e) {
            $this->logger->logException($e);
            return $this->urlBuilder->getUrl('', ['_direct' => 'core/index/notFound']);
        }
    }
}
