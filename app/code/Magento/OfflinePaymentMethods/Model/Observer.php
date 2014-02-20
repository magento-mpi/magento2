<?php
    /**
     * {license_notice}
     *
     * @category    Magento
     * @package     Magento_Payment
     * @copyright   {copyright}
     * @license     {license_link}
     */

    /**
     * Payment Observer
     */
namespace Magento\OfflinePaymentMethods\Model;

class Observer
{
    /**
     * Construct
     */
    public function __construct() { }

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
