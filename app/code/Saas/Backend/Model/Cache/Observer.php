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
     * @var Saas_Saas_Helper_Data
     */
    protected $_saasHelper;

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param Saas_Saas_Helper_Data $saasHelper
     */
    public function __construct(Mage_Core_Controller_Request_Http $request, Saas_Saas_Helper_Data $saasHelper)
    {
        $this->_request = $request;
        $this->_saasHelper = $saasHelper;
    }

    /**
     * Redirects to noRoute from actions of admin cache controller
     *
     * @param Varien_Event_Observer $observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function disableAdminhtmlCacheController(Varien_Event_Observer $observer)
    {
        if ($this->_request->getControllerModule() == 'Mage_Adminhtml'
            && $this->_request->getControllerName() == 'cache'
        ) {
            $this->_saasHelper->customizeNoRoutForward($this->_request);
        }
    }
}
