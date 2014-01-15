<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App;

class View implements ViewInterface
{
    /**
     * @var \Magento\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var \Magento\Config\ScopeInterface
     */
    protected $_configScope;

    /**
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\TranslateInterface
     */
    protected $_translator;

    /**
     * @var \Magento\App\ActionFlag
     */
    protected $_actionFlag;

    /**
     * @var \Magento\App\ResponseInterface
     */
    protected $_response;

    /**
     * @var \Magento\App\RequestInterface
     */
    protected $_request;

    /**
     * @var bool
     */
    protected $_isLayoutLoaded = false;

    /**
     * @param \Magento\View\LayoutInterface $layout
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\App\ResponseInterface $response
     * @param \Magento\Config\ScopeInterface $configScope
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\TranslateInterface $translator
     * @param \Magento\App\ActionFlag $actionFlag
     */
    public function __construct(
        \Magento\View\LayoutInterface $layout,
        \Magento\App\RequestInterface $request,
        \Magento\App\ResponseInterface $response,
        \Magento\Config\ScopeInterface $configScope,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\TranslateInterface $translator,
        \Magento\App\ActionFlag $actionFlag
    ) {
        $this->_layout = $layout;
        $this->_request = $request;
        $this->_response = $response;
        $this->_configScope = $configScope;
        $this->_eventManager = $eventManager;
        $this->_translator = $translator;
        $this->_actionFlag = $actionFlag;
    }

    /**
     * Retrieve current layout object
     *
     * @return \Magento\View\LayoutInterface
     */
    public function getLayout()
    {
        $this->_layout->setArea($this->_configScope->getCurrentScope());
        return $this->_layout;
    }

    /**
     * Load layout by handles(s)
     *
     * @param   string|null|bool $handles
     * @param   bool $generateBlocks
     * @param   bool $generateXml
     * @return  $this
     * @throws  \RuntimeException
     */
    public function loadLayout($handles = null, $generateBlocks = true, $generateXml = true)
    {
        if ($this->_isLayoutLoaded) {
            throw new \RuntimeException('Layout must be loaded only once.');
        }
        // if handles were specified in arguments load them first
        if (false !== $handles && '' !== $handles) {
            $this->getLayout()->getUpdate()->addHandle($handles ? $handles : 'default');
        }

        // add default layout handles for this action
        $this->addActionLayoutHandles();

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
     * @return bool
     */
    public function addPageLayoutHandles(array $parameters = array())
    {
        $handle = $this->getDefaultLayoutHandle();
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
        \Magento\Profiler::start('LAYOUT');

        // dispatch event for adding handles to layout update
        $this->_eventManager->dispatch(
            'controller_action_layout_load_before',
            array('full_action_name' => $this->_request->getFullActionName(), 'layout' => $this->getLayout())
        );

        // load layout updates by specified handles
        \Magento\Profiler::start('layout_load');
        $this->getLayout()->getUpdate()->load();
        \Magento\Profiler::stop('layout_load');

        \Magento\Profiler::stop('LAYOUT');
        return $this;
    }

    /**
     * Generate layout xml
     *
     * @return $this
     */
    public function generateLayoutXml()
    {
        \Magento\Profiler::start('LAYOUT');
        // generate xml from collected text updates
        \Magento\Profiler::start('layout_generate_xml');
        $this->getLayout()->generateXml();
        \Magento\Profiler::stop('layout_generate_xml');

        \Magento\Profiler::stop('LAYOUT');
        return $this;
    }

    /**
     * Generate layout blocks
     *
     * @return $this
     */
    public function generateLayoutBlocks()
    {
        \Magento\Profiler::start('LAYOUT');

        // dispatch event for adding xml layout elements
        if (!$this->_actionFlag->get('', \Magento\App\Action\Action::FLAG_NO_DISPATCH_BLOCK_EVENT)) {
            $this->_eventManager->dispatch(
                'controller_action_layout_generate_blocks_before',
                array('full_action_name' => $this->_request->getFullActionName(), 'layout' => $this->getLayout())
            );
        }

        // generate blocks from xml layout
        \Magento\Profiler::start('layout_generate_blocks');
        $this->getLayout()->generateElements();
        \Magento\Profiler::stop('layout_generate_blocks');

        if (!$this->_actionFlag->get('', \Magento\App\Action\Action::FLAG_NO_DISPATCH_BLOCK_EVENT)) {
            $this->_eventManager->dispatch(
                'controller_action_layout_generate_blocks_after',
                array('full_action_name' => $this->_request->getFullActionName(), 'layout' => $this->getLayout())
            );
        }

        \Magento\Profiler::stop('LAYOUT');
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

        \Magento\Profiler::start('LAYOUT');

        \Magento\Profiler::start('layout_render');

        if ('' !== $output) {
            $this->getLayout()->addOutputElement($output);
        }

        $this->_eventManager->dispatch('controller_action_layout_render_before');
        $this->_eventManager->dispatch(
            'controller_action_layout_render_before_' . $this->_request->getFullActionName()
        );

        $output = $this->getLayout()->getOutput();
        $this->_translator->processResponseBody($output);
        $this->_response->appendBody($output);
        \Magento\Profiler::stop('layout_render');

        \Magento\Profiler::stop('LAYOUT');
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
}
