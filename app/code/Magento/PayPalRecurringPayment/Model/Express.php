<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PayPalRecurringPayment\Model;

use Magento\Payment\Model\Info as PaymentInfo;
use Magento\Paypal\Model\Express as PayPalExpress;
use Magento\RecurringPayment\Model\ManagerInterface;
use Magento\RecurringPayment\Model\RecurringPayment;
use Magento\RecurringPayment\Model\States;

class Express implements ManagerInterface
{
    /**
     * @var PayPalExpress
     */
    protected $_paymentMethod;

    /**
     * @param PayPalExpress $paymentMethod
     */
    public function __construct(PayPalExpress $paymentMethod)
    {
        $this->_paymentMethod = $paymentMethod;
    }

    /**
     * Get  Payment Method code
     *
     * @return string
     */
    public function getPaymentMethodCode()
    {
        return $this->_paymentMethod->getCode();
    }

    /**
     * Validate RP data
     *
     * @param RecurringPayment $payment
     * @return void
     * @throws \Magento\Framework\Model\Exception
     */
    public function validate(RecurringPayment $payment)
    {
        $errors = [];
        if (strlen($payment->getSubscriberName()) > 32) {
            // up to 32 single-byte chars
            $errors[] = __('The subscriber name is too long.');
        }
        $refId = $payment->getInternalReferenceId();
        // up to 127 single-byte alphanumeric
        if (strlen($refId) > 127) {
            //  || !preg_match('/^[a-z\d\s]+$/i', $refId)
            $errors[] = __('The merchant\'s reference ID format is not supported.');
        }
        $scheduleDescription = $payment->getScheduleDescription();
        // up to 127 single-byte alphanumeric
        if (strlen($scheduleDescription) > 127) {
            //  || !preg_match('/^[a-z\d\s]+$/i', $scheduleDescription)
            $errors[] = __('The schedule description is too long.');
        }
        if ($errors) {
            throw new \Magento\Framework\Model\Exception(implode(' ', $errors));
        }
    }

    /**
     * Submit RP to the gateway
     *
     * @param RecurringPayment $payment
     * @param PaymentInfo $paymentInfo
     * @return void
     */
    public function submit(RecurringPayment $payment, PaymentInfo $paymentInfo)
    {
        $token = $paymentInfo->getAdditionalInformation(PayPalExpress\Checkout::PAYMENT_INFO_TRANSPORT_TOKEN);
        $payment->setToken($token);
        $api = $this->_paymentMethod->getApi();
        \Magento\Framework\Object\Mapper::accumulateByMap(
            $payment,
            $api,
            [
                'token', // EC fields
                // TODO: DP fields
                // payment fields
                'subscriber_name',
                'start_datetime',
                'internal_reference_id',
                'schedule_description',
                'suspension_threshold',
                'bill_failed_later',
                'period_unit',
                'period_frequency',
                'period_max_cycles',
                'billing_amount' => 'amount',
                'trial_period_unit',
                'trial_period_frequency',
                'trial_period_max_cycles',
                'trial_billing_amount',
                'currency_code',
                'shipping_amount',
                'tax_amount',
                'init_amount',
                'init_may_fail'
            ]
        );
        $api->callCreateRecurringPayment();
        $payment->setReferenceId($api->getRecurringPaymentId());
        if ($api->getIsPaymentActive()) {
            $payment->setState(States::ACTIVE);
        } elseif ($api->getIsPaymentPending()) {
            $payment->setState(States::PENDING);
        }
    }

    /**
     * Fetch RP details
     *
     * @param string $referenceId
     * @param \Magento\Framework\Object $result
     * @return void
     */
    public function getDetails($referenceId, \Magento\Framework\Object $result)
    {
        $this->_paymentMethod->getApi()->setRecurringPaymentId($referenceId)->callGetRecurringPaymentDetails($result);
    }

    /**
     * Whether can get recurring payment details
     *
     * @return bool
     */
    public function canGetDetails()
    {
        return true;
    }

    /**
     * Update RP data
     *
     * @param RecurringPayment $payment
     * @return void
     */
    public function update(RecurringPayment $payment)
    {
    }

    /**
     * Manage status
     *
     * @param RecurringPayment $payment
     * @return void
     */
    public function updateStatus(RecurringPayment $payment)
    {
        $api = $this->_paymentMethod->getApi();
        $action = null;
        switch ($payment->getNewState()) {
            case States::CANCELED:
                $action = 'cancel';
                break;
            case States::SUSPENDED:
                $action = 'suspend';
                break;
            case States::ACTIVE:
                $action = 'activate';
                break;
        }
        $state = $payment->getState();
        $api->setRecurringPaymentId(
            $payment->getReferenceId()
        )->setIsAlreadyCanceled(
            $state == States::CANCELED
        )->setIsAlreadySuspended(
            $state == States::SUSPENDED
        )->setIsAlreadyActive(
            $state == States::ACTIVE
        )->setAction(
            $action
        )->callManageRecurringPaymentStatus();
    }
}
