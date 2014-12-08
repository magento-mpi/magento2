<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Controller\Adminhtml\RecurringPayment;

use Magento\Framework\Model\Exception as CoreException;

class View extends \Magento\RecurringPayment\Controller\Adminhtml\RecurringPayment
{
    /**
     * View recurring payment details
     *
     * @return void
     */
    public function execute()
    {
        try {
            $payment = $this->_initPayment();
            $this->_view->loadLayout();
            $this->_setActiveMenu('Magento_RecurringPayment::recurring_payment');
            $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Recurring Billing Payments'));
            $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Payment #%1', $payment->getReferenceId()));
            $this->_view->renderLayout();
            return;
        } catch (CoreException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_logger->logException($e);
        }
        $this->_redirect('sales/*/');
    }
}
