<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Controller\Adminhtml\Billing\Agreement;

class Index extends \Magento\Paypal\Controller\Adminhtml\Billing\Agreement
{
    /**
     * Billing agreements
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Paypal::paypal_billing_agreement');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Billing Agreements'));
        $this->_view->renderLayout();
    }
}
