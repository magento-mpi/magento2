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
 * A generic layout response can be used for rendering any kind of layout
 * So it comprises a response body from the layout elements it has and sets it to the HTTP response
 */
class Layout extends View\Element\Template
    //implements ResultInterface
{
    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * @var \Magento\Framework\Translate\InlineInterface
     */
    protected $translateInline;

    /**
     * @param View\Element\Template\Context $context
     * @param View\LayoutFactory $layoutFactory
     * @param \Magento\Framework\Translate\InlineInterface $translateInline
     * @param array $data
     */
    public function __construct(
        View\Element\Template\Context $context,
        View\LayoutFactory $layoutFactory,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        array $data = array()
    ) {
        $this->layoutFactory = $layoutFactory;
        $this->translateInline = $translateInline;
        $this->setTemplate('Magento_Theme::root.phtml');
        parent::__construct($context, $data);
    }

    /**
     * Get layout instance for current page
     *
     * TODO: This layout model must be isolated, now are used shared instance of layout
     *
     * @return \Magento\Framework\View\Layout
     */
    public function getLayout()
    {
        if ($this->layout === null) {
            // TODO: This section will be removed in MAGETWO-26282
            $this->initLayout();
        }
        return $this->_layout;
    }

    /**
     * Create new instance of layout for current page
     *
     * @return $this
     */
    protected function initLayout()
    {
        $this->layout = $this->layoutFactory->create();
        return $this;
    }

    /**
     * @param ResponseInterface $response
     */
    public function renderResult(ResponseInterface $response)
    {
        $layout = $this->getLayout();

        $this->assign('headContent', $layout->getBlock('head')->toHtml());
        $layout->unsetElement('head');

        $output = $layout->getOutput();
        $this->translateInline->processResponseBody($output);
        $this->assign('layoutContent', $output);
        // TODO: implement assign for variables: bodyClasses, bodyAttributes
        $response->appendBody($this->toHtml());
    }
}
