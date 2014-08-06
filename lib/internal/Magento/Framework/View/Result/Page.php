<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\View\Result;

use Magento\Framework\View;
use Magento\Framework\App\RequestInterface;
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
 */
class Page extends Layout
{
    /**
     * Default template
     */
    const DEFAULT_ROOT_TEMPLATE = 'Magento_Theme::root.phtml';

    /**
     * @var string
     */
    protected $pageType;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * Constructor
     *
     * @param View\Element\Template\Context $context
     * @param View\LayoutFactory $layoutFactory
     * @param \Magento\Framework\Translate\InlineInterface $translateInline
     * @param \Magento\Framework\App\RequestInterface $request
     * @param View\Page\Config $pageConfig
     * @param string $pageType
     * @param array $data
     */
    public function __construct(
        View\Element\Template\Context $context,
        View\LayoutFactory $layoutFactory,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        RequestInterface $request,
        View\Page\Config $pageConfig,
        $pageType,
        array $data = array()
    ) {
        $this->request = $request;
        $this->pageConfig = $pageConfig;
        $this->pageType = $pageType;
        parent::__construct($context, $layoutFactory, $translateInline, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function initLayout()
    {
        parent::initLayout();
        $update = $this->getLayout()->getUpdate();
        $update->addHandle('default');
        $this->addPageLayoutHandles([], $this->getDefaultLayoutHandle());
        $this->addDefaultPageLayout();
        return $this;
    }

    /**
     * Add default page layout regarding definition in layout
     *
     * @return $this
     */
    protected function addDefaultPageLayout()
    {
        $update = $this->getLayout()->getUpdate();
        $defaultPageLayout = $update->isLayoutDefined() ? null : $update->getDefaultPageLayout() ;
        $pageLayout = $this->pageConfig->getPageLayout() ?: $defaultPageLayout;
        if ($pageLayout) {
            $update->addHandle($pageLayout);
            $this->setTemplate(self::DEFAULT_ROOT_TEMPLATE);
        }
        return $this;
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
        return $this->getLayout()->getUpdate()->addPageHandles($pageHandles);
    }

    /**
     * Retrieve the default layout handle name for the current action
     *
     * @return string
     */
    public function getDefaultLayoutHandle()
    {
        return strtolower($this->request->getFullActionName());
    }

    /**
     * @param ResponseInterface $response
     * @return $this
     */
    public function renderResult(ResponseInterface $response)
    {
        if ($this->getTemplate()) {
            $layout = $this->getLayout();
            $this->assign('headContent', $layout->getBlock('head')->toHtml());
            $layout->unsetElement('head');
            $output = $layout->getOutput();
            $this->translateInline->processResponseBody($output);
            $this->assign('layoutContent', $output);
            // TODO: implement assign for variables: bodyClasses, bodyAttributes
            $response->appendBody($this->toHtml());
        } else {
            parent::renderResult($response);
        }
        return $this;
    }
}
