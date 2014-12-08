<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Controller\Adminhtml\Billing\Agreement;

class Delete extends \Magento\Paypal\Controller\Adminhtml\Billing\Agreement
{
    /**
     * Delete billing agreement action
     *
     * @return void
     */
    public function execute()
    {
        $agreementModel = $this->_initBillingAgreement();

        if ($agreementModel) {
            try {
                $agreementModel->delete();
                $this->messageManager->addSuccess(__('You deleted the billing agreement.'));
                $this->_redirect('paypal/*/');
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(__('We could not delete the billing agreement.'));
                $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
            }
            $this->_redirect('paypal/*/view', ['_current' => true]);
        }
        $this->_redirect('paypal/*/');
    }
}
