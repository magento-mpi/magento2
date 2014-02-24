<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * OfflinePaymentMethods Observer
 */
namespace Magento\OfflinePaymentMethods\Model;

class Observer
{
    /**
     * Sets current instructions for bank transfer account
     *
     * @param \Magento\Event\Observer $observer
     * @return void
     */
    public function beforeOrderPaymentSave(\Magento\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $observer->getEvent()->getPayment();
        $banktransfer = \Magento\OfflinePaymentMethods\Model\Banktransfer::PAYMENT_METHOD_BANKTRANSFER_CODE;
        if($payment->getMethod() === $banktransfer) {
            $payment->setAdditionalInformation('instructions', $payment->getMethodInstance()->getInstructions());
        }
    }
}
