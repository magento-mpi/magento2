<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\View\Result;

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
     * Constructor
     *
     * @param View\Element\Template\Context $context
     * @param View\LayoutFactory $layoutFactory
     * @param \Magento\Framework\Translate\InlineInterface $translateInline
     * @param \Magento\Framework\View\Page\Config\Renderer $pageConfigRenderer
     * @param string $template
     * @param array $data
     */
    public function __construct(
        View\Element\Template\Context $context,
        View\LayoutFactory $layoutFactory,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        View\Page\Config\Renderer $pageConfigRenderer,
        $template,
        array $data = array()
    ) {
        parent::__construct($context, $layoutFactory, $translateInline, $data);
        $this->pageConfigRenderer = $pageConfigRenderer;
        $this->_template = $template;
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
        return $this->getLayout()->getUpdate()->addHandle($pageHandles);
    }

    /**
     * Retrieve the default layout handle name for the current action
     *
     * @return string
     */
    public function getDefaultLayoutHandle()
    {
        return strtolower($this->_request->getFullActionName());
    }

    /**
     * @param ResponseInterface $response
     * @return $this
     */
    protected function _renderResult(ResponseInterface $response)
    {
        if ($this->getConfig()->getPageLayout()) {
            $config = $this->getConfig();

            $this->addDefaultBodyClasses();
            $this->assign([
                'requireJs' => $this->getLayout()->getBlock('require.js')->toHtml(),
                'headContent' => $this->pageConfigRenderer->renderHeadContent(),
                'htmlAttributes' => $this->pageConfigRenderer->renderElementAttributes($config::ELEMENT_TYPE_HTML),
                'headAttributes' => $this->pageConfigRenderer->renderElementAttributes($config::ELEMENT_TYPE_HEAD),
                'bodyAttributes' => $this->pageConfigRenderer->renderElementAttributes($config::ELEMENT_TYPE_BODY)
            ]);

            $output = $this->getLayout()->getOutput();
            $this->translateInline->processResponseBody($output);
            $this->assign('layoutContent', $output);
            $response->appendBody($this->toHtml());
        } else {
            parent::_renderResult($response);
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
        $this->pageConfig->addBodyClass($this->_request->getFullActionName('-'));
        $pageLayout = $this->pageConfig->getPageLayout();
        if ($pageLayout) {
            $this->pageConfig->addBodyClass('page-layout-' . $pageLayout);
        }
        return $this;
    }
}
