<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App;

class View implements ViewInterface
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var \Magento\Framework\Config\ScopeInterface
     */
    protected $_configScope;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\Framework\Translate\InlineInterface
     */
    protected $_translateInline;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $pageFactory;

    /**
     * @var ActionFlag
     */
    protected $_actionFlag;

    /**
     * @var ResponseInterface
     */
    protected $_response;

    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var bool
     */
    protected $_isLayoutLoaded = false;

    /**
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param \Magento\Framework\Config\ScopeInterface $configScope
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Translate\InlineInterface $translateInline
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param ActionFlag $actionFlag
     */
    public function __construct(
        \Magento\Framework\View\LayoutInterface $layout,
        RequestInterface $request,
        ResponseInterface $response,
        \Magento\Framework\Config\ScopeInterface $configScope,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        ActionFlag $actionFlag
    ) {
        $this->_layout = $layout;
        $this->_request = $request;
        $this->_response = $response;
        $this->_configScope = $configScope;
        $this->_eventManager = $eventManager;
        $this->_translateInline = $translateInline;
        $this->pageFactory = $pageFactory;
        $this->_actionFlag = $actionFlag;
    }

    /**
     * Retrieve current layout object
     *
     * @return \Magento\Framework\View\LayoutInterface
     */
    public function getLayout()
    {
        return $this->_layout;
    }

    /**
     * {@inheritdoc}
     */
    public function loadLayout($handles = null, $generateBlocks = true, $generateXml = true, $addActionHandles = true)
    {
        if ($this->_isLayoutLoaded) {
            throw new \RuntimeException('Layout must be loaded only once.');
        }
        // if handles were specified in arguments load them first
        if (false !== $handles && '' !== $handles) {
            $this->getLayout()->getUpdate()->addHandle($handles ? $handles : 'default');
        }

        if ($addActionHandles) {
            // add default layout handles for this action
            $this->addActionLayoutHandles();
        }
        $this->loadLayoutUpdates();

        if (!$generateXml) {
            return $this;
        }
        $this->generateLayoutXml();

        if (!$generateBlocks) {
            return $this;
        }
        $this->generateLayoutBlocks();
        $this->_isLayoutLoaded = true;

        return $this;
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
     * Add layout handle by full controller action name
     *
     * @return $this
     */
    public function addActionLayoutHandles()
    {
        if (!$this->addPageLayoutHandles()) {
            $this->getLayout()->getUpdate()->addHandle($this->getDefaultLayoutHandle());
        }
        return $this;
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
     * Load layout updates
     *
     * @return $this
     */
    public function loadLayoutUpdates()
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
     * @return $this
     */
    public function generateLayoutBlocks()
    {
        \Magento\Framework\Profiler::start('LAYOUT');

        // dispatch event for adding xml layout elements
        if (!$this->_actionFlag->get('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH_BLOCK_EVENT)) {
            $this->_eventManager->dispatch(
                'controller_action_layout_generate_blocks_before',
                array('full_action_name' => $this->_request->getFullActionName(), 'layout' => $this->getLayout())
            );
        }

        // generate blocks from xml layout
        \Magento\Framework\Profiler::start('layout_generate_blocks');
        $this->getLayout()->generateElements();
        \Magento\Framework\Profiler::stop('layout_generate_blocks');

        if (!$this->_actionFlag->get('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH_BLOCK_EVENT)) {
            $this->_eventManager->dispatch(
                'controller_action_layout_generate_blocks_after',
                array('full_action_name' => $this->_request->getFullActionName(), 'layout' => $this->getLayout())
            );
        }

        \Magento\Framework\Profiler::stop('LAYOUT');
        return $this;
    }

    /**
     * Rendering layout
     *
     * @param   string $output
     * @return  $this
     */
    public function renderLayout($output = '')
    {
        if ($this->_actionFlag->get('', 'no-renderLayout')) {
            return $this;
        }

        \Magento\Framework\Profiler::start('LAYOUT');

        \Magento\Framework\Profiler::start('layout_render');

        if ('' !== $output) {
            $this->getLayout()->addOutputElement($output);
        }

        $this->_eventManager->dispatch('controller_action_layout_render_before');
        $this->_eventManager->dispatch(
            'controller_action_layout_render_before_' . $this->_request->getFullActionName()
        );

//        $output = $this->getLayout()->getOutput();
//        $this->_translateInline->processResponseBody($output);
//        $this->_response->appendBody($output);
        $page = $this->pageFactory->create($this->_request);
        $page->renderResult($this->_response);
        \Magento\Framework\Profiler::stop('layout_render');

        \Magento\Framework\Profiler::stop('LAYOUT');
        return $this;
    }

    /**
     * Set isLayoutLoaded flag
     *
     * @param bool $value
     * @return void
     */
    public function setIsLayoutLoaded($value)
    {
        $this->_isLayoutLoaded = $value;
    }

    /**
     * Returns is layout loaded
     *
     * @return bool
     */
    public function isLayoutLoaded()
    {
        return $this->_isLayoutLoaded;
    }
}
