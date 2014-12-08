<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Controller\RecurringPayment;

class UpdateState extends \Magento\RecurringPayment\Controller\RecurringPayment
{
    /**
     * Attempt to set payment state
     *
     * @return void
     */
    public function execute()
    {
        $payment = null;
        try {
            $payment = $this->_initPayment();

            switch ($this->getRequest()->getParam('action')) {
                case 'cancel':
                    $payment->cancel();
                    break;
                case 'suspend':
                    $payment->suspend();
                    break;
                case 'activate':
                    $payment->activate();
                    break;
                default:
                    break;
            }
            $this->messageManager->addSuccess(__('The payment state has been updated.'));
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We couldn\'t update the payment.'));
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
        }
        if ($payment) {
            $this->_redirect('*/*/view', ['payment' => $payment->getId()]);
        } else {
            $this->_redirect('*/*/');
        }
    }
}
