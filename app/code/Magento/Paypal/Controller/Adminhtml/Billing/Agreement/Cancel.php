<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Controller\Adminhtml\Billing\Agreement;

class Cancel extends \Magento\Paypal\Controller\Adminhtml\Billing\Agreement
{
    /**
     * Cancel billing agreement action
     *
     * @return void
     */
    public function execute()
    {
        $agreementModel = $this->_initBillingAgreement();

        if ($agreementModel && $agreementModel->canCancel()) {
            try {
                $agreementModel->cancel();
                $this->messageManager->addSuccess(__('You canceled the billing agreement.'));
                $this->_redirect('paypal/*/view', ['_current' => true]);
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(__('We could not cancel the billing agreement.'));
                $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
            }
            $this->_redirect('paypal/*/view', ['_current' => true]);
        }
        return $this->_redirect('paypal/*/');
    }
}
