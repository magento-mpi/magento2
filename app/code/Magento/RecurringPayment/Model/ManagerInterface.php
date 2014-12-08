<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Model;

use Magento\Framework\Object;
use Magento\Payment\Model\Info as PaymentInfo;

/**
 * Recurring payment gateway management interface
 */
interface ManagerInterface
{
    /**
     * Validate data
     *
     * @param RecurringPayment $payment
     * @return void
     * @throws \Magento\Framework\Model\Exception
     */
    public function validate(RecurringPayment $payment);

    /**
     * Submit to the gateway
     *
     * @param RecurringPayment $payment
     * @param PaymentInfo $paymentInfo
     * @return void
     */
    public function submit(RecurringPayment $payment, PaymentInfo $paymentInfo);

    /**
     * Fetch details
     *
     * @param string $referenceId
     * @param \Magento\Framework\Object $result
     * @return void
     */
    public function getDetails($referenceId, Object $result);

    /**
     * Check whether can get recurring payment details
     *
     * @return bool
     */
    public function canGetDetails();

    /**
     * Update data
     *
     * @param RecurringPayment $payment
     * @return void
     */
    public function update(RecurringPayment $payment);

    /**
     * Manage status
     *
     * @param RecurringPayment $payment
     * @return void
     */
    public function updateStatus(RecurringPayment $payment);

    /**
     * Get  Payment Method code
     *
     * @return string
     */
    public function getPaymentMethodCode();
}
