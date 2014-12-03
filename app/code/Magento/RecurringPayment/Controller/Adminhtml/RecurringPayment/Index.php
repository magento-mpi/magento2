<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Controller\Adminhtml\RecurringPayment;

class Index extends \Magento\RecurringPayment\Controller\Adminhtml\RecurringPayment
{
    /**
     * Recurring payments list
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_RecurringPayment::recurring_payment');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Recurring Billing Payments'));
        $this->_view->renderLayout();
    }
}
