<?php
/**
 * Observer for the Saas_Customer module
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Customer_Model_Customer_Online_Observer
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
    public function __construct(Mage_Core_Controller_Request_Http $request,  Saas_Saas_Helper_Data $saasHelper)
    {
        $this->_request = $request;
        $this->_saasHelper = $saasHelper;
    }

    /**
     * Forward customer online controller on noRoute
     *
     * @param Varien_Event_Observer $observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function disableCustomerOnlineController(Varien_Event_Observer $observer)
    {
        if ($this->_request->getControllerModule() == 'Mage_Adminhtml'
            && $this->_request->getControllerName() == 'customer_online'
        ) {
            $this->_saasHelper->customizeNoRoutForward($this->_request);
        }
    }
}
