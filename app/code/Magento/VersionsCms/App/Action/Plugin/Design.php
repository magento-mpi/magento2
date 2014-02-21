<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\App\Action\Plugin;

class Design
{
    /**
     * @var \Magento\View\DesignLoader
     */
    protected $_designLoader;

    /**
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * @var \Magento\App\RequestInterface
     */
    protected $_request;

    /**
     * @param \Magento\View\DesignLoader $designLoader
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\App\State $appState
     */
    public function __construct(
        \Magento\View\DesignLoader $designLoader,
        \Magento\App\RequestInterface $request,
        \Magento\App\State $appState
    ) {
        $this->_request = $request;
        $this->_appState = $appState;
        $this->_designLoader = $designLoader;
    }

    /**
     * Initialize design
     *
     * @param array $arguments
     * @return array
     */
    public function beforeDispatch(array $arguments = array())
    {
        if ($this->_request->getActionName() == 'drop') {
            $this->_appState->emulateAreaCode('frontend', array($this, 'emulateDesignCallback'));
        } else {
            $this->_designLoader->load();
        }
        return $arguments;
    }

    /**
     * Callback for init design from outside (need to substitute area code)
     *
     * @return void
     */
    public function emulateDesignCallback()
    {
        $this->_designLoader->load();
    }
}
