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

class UpdatePayment extends \Magento\RecurringPayment\Controller\Adminhtml\RecurringPayment
{
    /**
     * Payment information updater action
     *
     * @return void
     */
    public function execute()
    {
        $payment = null;
        try {
            $payment = $this->_initPayment();
            $payment->fetchUpdate();
            if ($payment->hasDataChanges()) {
                $payment->save();
                $this->messageManager->addSuccess(__('You updated the payment.'));
            } else {
                $this->messageManager->addNotice(__('The payment has no changes.'));
            }
        } catch (CoreException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We could not update the payment.'));
            $this->_logger->logException($e);
        }
        if ($payment) {
            $this->_redirect('sales/*/view', [self::PARAM_PAYMENT => $payment->getId()]);
        } else {
            $this->_redirect('sales/*/');
        }
    }
}
