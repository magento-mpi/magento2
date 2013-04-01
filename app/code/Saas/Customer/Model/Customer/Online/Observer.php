<?php
/**
 * Observer for the Saas_Customer module
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Customer_Model_Customer_Online_Observer extends Saas_Saas_Model_Observer_Controller_LimitationAbstract
{
    /**
     * Forward customer online controller on noRoute
     *
     * @param Varien_Event_Observer $observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function disableCustomerOnlineController(Varien_Event_Observer $observer)
    {
        if ($this->_request->getControllerName() == 'customer_online'
            && $this->_request->getControllerModule() == 'Mage_Adminhtml'
        ) {
            $this->_saasHelper->customizeNoRoutForward($this->_request);
        }
    }
}
