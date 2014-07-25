<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Controller\Adminhtml\Billing\Agreement;

class View extends \Magento\Paypal\Controller\Adminhtml\Billing\Agreement
{
    /**
     * View billing agreement action
     *
     * @return void
     */
    public function execute()
    {
        $agreementModel = $this->_initBillingAgreement();

        if ($agreementModel) {
            $this->_title->add(__('Billing Agreements'));
            $this->_title->add(sprintf("#%s", $agreementModel->getReferenceId()));

            $this->_view->loadLayout();
            $this->_setActiveMenu('Magento_Paypal::paypal_billing_agreement');
            $this->_view->renderLayout();
            return;
        }

        $this->_redirect('paypal/*/');
        return;
    }
}
