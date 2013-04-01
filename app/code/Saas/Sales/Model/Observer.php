<?php
/**
 * Sales observer
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_Sales_Model_Observer extends Saas_Saas_Model_Observer_Controller_Limitations_Abstract
{
    /**
     * Redirects to noRoute from actions of admin sales transactions controller
     *
     * @param Varien_Event_Observer $observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function disableAdminhtmlSalesTransactionsController(Varien_Event_Observer $observer = null)
    {
        if ($this->_request->getControllerName() == "sales_transactions"
            && $this->_request->getControllerModule() == 'Mage_Adminhtml'
        ) {
            $this->_saasHelper->customizeNoRoutForward($this->_request);
        }
    }
}
