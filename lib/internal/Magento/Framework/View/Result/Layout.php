<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\View\Result;

use Magento\Framework\View;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\ResponseInterface;

/**
 * A generic layout response can be used for rendering any kind of layout
 * So it comprises a response body from the layout elements it has and sets it to the HTTP response
 */
class Layout extends View\Element\Template
    implements ResultInterface
{
    /**
     * Temporary state flag to know where page was created
     * Default value is false, it means that the page was created in App\View model
     *
     * @var bool
     */
    protected  $isControllerPage = false;

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
     * Constructor
     *
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
        $this->layout = $this->layoutFactory->create();
        $this->translateInline = $translateInline;
        parent::__construct($context, $data);
    }

    /**
     * Get layout instance for current page
     *
     * @return \Magento\Framework\View\Layout
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @return $this
     */
    public function initLayout()
    {
        return $this;
    }

    /**
     * @param string|string[] $handleName
     * @return $this
     */
    public function addHandle($handleName)
    {
        $this->getLayout()->getUpdate()->addHandle($handleName);
        return $this;
    }

    /**
     * Add update to merge object
     *
     * @param string $update
     * @return $this
     */
    public function addUpdate($update)
    {
        $this->getLayout()->getUpdate()->addUpdate($update);
        return $this;
    }

    /**
     * Load layout updates
     *
     * @return $this
     */
    protected function loadLayoutUpdates()
    {
        \Magento\Framework\Profiler::start('LAYOUT');
        // dispatch event for adding handles to layout update
        $this->_eventManager->dispatch(
            'controller_action_layout_load_before',
            array('full_action_name' => $this->_request->getFullActionName(), 'layout' => $this->getLayout())
        );
        // load layout updates by specified handles
        \Magento\Framework\Profiler::start('layout_load');
        $this->getLayout()->getUpdate()->load();
        \Magento\Framework\Profiler::stop('layout_load');
        \Magento\Framework\Profiler::stop('LAYOUT');
        return $this;
    }

    /**
     * Generate layout xml
     *
     * @return $this
     */
    public function generateLayoutXml()
    {
        \Magento\Framework\Profiler::start('LAYOUT');
        // generate xml from collected text updates
        \Magento\Framework\Profiler::start('layout_generate_xml');
        $this->getLayout()->generateXml();
        \Magento\Framework\Profiler::stop('layout_generate_xml');
        \Magento\Framework\Profiler::stop('LAYOUT');
        return $this;
    }

    /**
     * Generate layout blocks
     *
     * TODO: Restore action flag functionality to have ability to turn off event dispatching
     *
     * @return $this
     */
    public function generateLayoutBlocks()
    {
        \Magento\Framework\Profiler::start('LAYOUT');
        // dispatch event for adding xml layout elements
        $this->_eventManager->dispatch(
            'controller_action_layout_generate_blocks_before',
            array('full_action_name' => $this->_request->getFullActionName(), 'layout' => $this->getLayout())
        );
        // generate blocks from xml layout
        \Magento\Framework\Profiler::start('layout_generate_blocks');
        $this->getLayout()->generateElements();
        \Magento\Framework\Profiler::stop('layout_generate_blocks');
        $this->_eventManager->dispatch(
            'controller_action_layout_generate_blocks_after',
            array('full_action_name' => $this->_request->getFullActionName(), 'layout' => $this->getLayout())
        );
        \Magento\Framework\Profiler::stop('LAYOUT');
        return $this;
    }

    /**
     * Render current layout
     *
     * @param ResponseInterface $response
     * @return $this
     */
    public function renderResult(ResponseInterface $response)
    {
        if ($this->isControllerPage) {
            $this->_initLayout();
        }
        \Magento\Framework\Profiler::start('LAYOUT');
        \Magento\Framework\Profiler::start('layout_render');
        $this->_renderResult($response);
        $this->_eventManager->dispatch('controller_action_layout_render_before');
        $this->_eventManager->dispatch(
            'controller_action_layout_render_before_' . $this->_request->getFullActionName()
        );
        \Magento\Framework\Profiler::stop('layout_render');
        \Magento\Framework\Profiler::stop('LAYOUT');
        return $this;
    }

    /**
     * Create new instance of layout for current page
     *
     * @return $this
     */
    protected function _initLayout()
    {
        $this->loadLayoutUpdates();
        $this->generateLayoutXml();
        $this->generateLayoutBlocks();
        return $this;
    }

    /**
     * Render current layout
     *
     * @param ResponseInterface $response
     * @return $this
     */
    protected function _renderResult(ResponseInterface $response)
    {
        $response->appendBody($this->_layout->getOutput());
        return $this;
    }

    /**
     * Set state of current instance
     *
     * @param bool $state
     * @return $this
     */
    public function setIsControllerPage($state)
    {
        $this->isControllerPage = $state;
        return $this;
    }
}
