<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PayPalRecurringPayment\Model;

use \Magento\Paypal\Model\Express as PayPalExpress;
use \Magento\Payment\Model\Info as PaymentInfo;
use \Magento\RecurringProfile\Model\States;
use \Magento\RecurringProfile\Model\RecurringProfile;
use \Magento\RecurringProfile\Model\MethodInterface;

class Express extends \Magento\Object implements MethodInterface
{
    /**
     * @var PayPalExpress
     */
    protected $_paymentMethod;

    /**
     * @param PayPalExpress $paymentMethod
     * @param array $data
     */
    public function __construct(PayPalExpress $paymentMethod, array $data = array())
    {
        $this->_paymentMethod = $paymentMethod;
        parent::__construct($data);
    }

    /**
     * Validate RP data
     *
     * @param RecurringProfile $profile
     * @throws \Magento\Core\Exception
     */
    public function validate(RecurringProfile $profile)
    {
        $errors = array();
        if (strlen($profile->getSubscriberName()) > 32) { // up to 32 single-byte chars
            $errors[] = __('The subscriber name is too long.');
        }
        $refId = $profile->getInternalReferenceId(); // up to 127 single-byte alphanumeric
        if (strlen($refId) > 127) { //  || !preg_match('/^[a-z\d\s]+$/i', $refId)
            $errors[] = __('The merchant\'s reference ID format is not supported.');
        }
        $profile->getScheduleDescription(); // up to 127 single-byte alphanumeric
        if (strlen($refId) > 127) { //  || !preg_match('/^[a-z\d\s]+$/i', $scheduleDescription)
            $errors[] = __('The schedule description is too long.');
        }
        if ($errors) {
            throw new \Magento\Core\Exception(implode(' ', $errors));
        }
    }

    /**
     * Submit RP to the gateway
     *
     * @param RecurringProfile $profile
     * @param PaymentInfo $paymentInfo
     */
    public function submit(RecurringProfile $profile, PaymentInfo $paymentInfo)
    {
        $token = $paymentInfo->getAdditionalInformation(PayPalExpress\Checkout::PAYMENT_INFO_TRANSPORT_TOKEN);
        $profile->setToken($token);
        $api = $this->_paymentMethod->getApi();
        \Magento\Object\Mapper::accumulateByMap(
            $profile,
            $api,
            array(
                'token', // EC fields
                // TODO: DP fields
                // profile fields
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
            )
        );
        $api->callCreateRecurringPaymentsProfile();
        $profile->setReferenceId($api->getRecurringProfileId());
        if ($api->getIsProfileActive()) {
            $profile->setState(States::ACTIVE);
        } elseif ($api->getIsProfilePending()) {
            $profile->setState(States::PENDING);
        }
    }

    /**
     * Fetch RP details
     *
     * @param string $referenceId
     * @param \Magento\Object $result
     */
    public function getDetails($referenceId, \Magento\Object $result)
    {
        $this->_paymentMethod->getApi()->setRecurringProfileId($referenceId)
            ->callGetRecurringPaymentsProfileDetails($result);
    }

    /**
     * Whether can get recurring profile details
     */
    public function canGetDetails()
    {
        return true;
    }

    /**
     * Update RP data
     *
     * @param RecurringProfile $profile
     */
    public function update(RecurringProfile $profile)
    {
    }

    /**
     * Manage status
     *
     * @param RecurringProfile $profile
     */
    public function updateStatus(RecurringProfile $profile)
    {
        $api = $this->_paymentMethod->getApi();
        $action = null;
        switch ($profile->getNewState()) {
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
        $state = $profile->getState();
        $api->setRecurringProfileId($profile->getReferenceId())
            ->setIsAlreadyCanceled($state == States::CANCELED)
            ->setIsAlreadySuspended($state == States::SUSPENDED)
            ->setIsAlreadyActive($state == States::ACTIVE)
            ->setAction($action)
            ->callManageRecurringPaymentsProfileStatus();
    }
}
