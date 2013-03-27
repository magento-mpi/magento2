<?php
/**
 * Cache observer
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_Backend_Model_Cache_Observer
{
    /**
     * @var Mage_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * @param Mage_Core_Controller_Request_Http $request
     */
    public function __construct(Mage_Core_Controller_Request_Http $request)
    {
        $this->_request = $request;
    }

    /**
     * Redirects to noRoute from actions of admin cache controller
     *
     * @param Varien_Event_Observer $observer
     */
    public function disableAdminhtmlCacheController(Varien_Event_Observer $observer)
    {
        if ($this->_request->getControllerName() == 'cache'
            && $this->_request->getControllerModule() == 'Mage_Adminhtml'
        ) {
            $this->_request->setRouteName('noRoute');
        }
    }
}
