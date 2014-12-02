<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Controller\RecurringPayment;

class Index extends \Magento\RecurringPayment\Controller\RecurringPayment
{
    /**
     * Payments listing
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Recurring Billing Payments'));
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }
}
