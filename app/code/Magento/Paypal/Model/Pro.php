<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * PayPal Website Payments Pro implementation for payment method instaces
 * This model was created because right now PayPal Direct and PayPal Express payment methods cannot have same abstract
 */
namespace Magento\Paypal\Model;

class Pro
{
    /**
     * Possible payment review actions (for FMF only)
     *
     * @var string
     */
    const PAYMENT_REVIEW_ACCEPT = 'accept';
    const PAYMENT_REVIEW_DENY = 'deny';

    /**
     * Config instance
     *
     * @var \Magento\Paypal\Model\Config
     */
    protected $_config = null;

    /**
     * API instance
     *
     * @var \Magento\Paypal\Model\Api\Nvp
     */
    protected $_api = null;

    /**
     * PayPal info object
     *
     * @var \Magento\Paypal\Model\Info
     */
    protected $_infoInstance = null;

    /**
     * API model type
     *
     * @var string
     */
    protected $_apiType = 'Magento\Paypal\Model\Api\Nvp';

    /**
     * Config model type
     *
     * @var string
     */
    protected $_configType = 'Magento\Paypal\Model\Config';

    /**
     * Payment method code setter. Also instantiates/updates config
     *
     * @param string $code
     * @param int|null $storeId
     */
    public function setMethod($code, $storeId = null)
    {
        if (null === $this->_config) {
            $params = array($code);
            if (null !== $storeId) {
                $params[] = $storeId;
            }
            $this->_config = \Mage::getModel($this->_configType, array('params' => $params));
        } else {
            $this->_config->setMethod($code);
            if (null !== $storeId) {
                $this->_config->setStoreId($storeId);
            }
        }
        return $this;
    }

    /**
     * Config instance setter
     *
     * @param \Magento\Paypal\Model\Config $instace
     * @param int $storeId
     */
    public function setConfig(\Magento\Paypal\Model\Config $instace, $storeId = null)
    {
        $this->_config = $instace;
        if (null !== $storeId) {
            $this->_config->setStoreId($storeId);
        }
        return $this;
    }

    /**
     * Config instance getter
     *
     * @return \Magento\Paypal\Model\Config
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * API instance getter
     * Sets current store id to current config instance and passes it to API
     *
     * @return \Magento\Paypal\Model\Api\Nvp
     */
    public function getApi()
    {
        if (null === $this->_api) {
            $this->_api = \Mage::getModel($this->_apiType);
        }
        $this->_api->setConfigObject($this->_config);
        return $this->_api;
    }

    /**
     * Destroy existing NVP Api object
     *
     * @return \Magento\Paypal\Model\Pro
     */
    public function resetApi()
    {
        $this->_api = null;

        return $this;
    }

    /**
     * Instantiate and return info model
     *
     * @return \Magento\Paypal\Model\Info
     */
    public function getInfo()
    {
        if (null === $this->_infoInstance) {
            $this->_infoInstance = \Mage::getModel('Magento\Paypal\Model\Info');
        }
        return $this->_infoInstance;
    }

    /**
     * Transfer transaction/payment information from API instance to order payment
     *
     * @param \Magento\Paypal\Model\Api\AbstractApi $from
     * @param \Magento\Payment\Model\Info $to
     * @return \Magento\Paypal\Model\Pro
     */
    public function importPaymentInfo(\Magento\Object $from, \Magento\Payment\Model\Info $to)
    {
        // update PayPal-specific payment information in the payment object
        $this->getInfo()->importToPayment($from, $to);

        /**
         * Detect payment review and/or frauds
         * PayPal pro API returns fraud results only in the payment call response
         */
        if ($from->getDataUsingMethod(\Magento\Paypal\Model\Info::IS_FRAUD)) {
            $to->setIsTransactionPending(true);
            $to->setIsFraudDetected(true);
        } elseif ($this->getInfo()->isPaymentReviewRequired($to)) {
            $to->setIsTransactionPending(true);
        }

        // give generic info about transaction state
        if ($this->getInfo()->isPaymentSuccessful($to)) {
            $to->setIsTransactionApproved(true);
        } elseif ($this->getInfo()->isPaymentFailed($to)) {
            $to->setIsTransactionDenied(true);
        }

        return $this;
    }

    /**
     * Void transaction
     *
     * @param \Magento\Object $payment
     */
    public function void(\Magento\Object $payment)
    {
        if ($authTransactionId = $this->_getParentTransactionId($payment)) {
            $api = $this->getApi();
            $api->setPayment($payment)->setAuthorizationId($authTransactionId)->callDoVoid();
            $this->importPaymentInfo($api, $payment);
        } else {
            \Mage::throwException(__('You need an authorization transaction to void.'));
        }
    }

    /**
     * Attempt to capture payment
     * Will return false if the payment is not supposed to be captured
     *
     * @param \Magento\Object $payment
     * @param float $amount
     * @return false|null
     */
    public function capture(\Magento\Object $payment, $amount)
    {
        $authTransactionId = $this->_getParentTransactionId($payment);
        if (!$authTransactionId) {
            return false;
        }
        $api = $this->getApi()
            ->setAuthorizationId($authTransactionId)
            ->setIsCaptureComplete($payment->getShouldCloseParentTransaction())
            ->setAmount($amount)
            ->setCurrencyCode($payment->getOrder()->getBaseCurrencyCode())
            ->setInvNum($payment->getOrder()->getIncrementId())
            // TODO: pass 'NOTE' to API
        ;

        $api->callDoCapture();
        $this->_importCaptureResultToPayment($api, $payment);
    }

    /**
     * Refund a capture transaction
     *
     * @param \Magento\Object $payment
     * @param float $amount
     */
    public function refund(\Magento\Object $payment, $amount)
    {
        $captureTxnId = $this->_getParentTransactionId($payment);
        if ($captureTxnId) {
            $api = $this->getApi();
            $order = $payment->getOrder();
            $api->setPayment($payment)
                ->setTransactionId($captureTxnId)
                ->setAmount($amount)
                ->setCurrencyCode($order->getBaseCurrencyCode())
            ;
            $canRefundMore = $payment->getCreditmemo()->getInvoice()->canRefund();
            $isFullRefund = !$canRefundMore
                && (0 == ((float)$order->getBaseTotalOnlineRefunded() + (float)$order->getBaseTotalOfflineRefunded()));
            $api->setRefundType($isFullRefund ? \Magento\Paypal\Model\Config::REFUND_TYPE_FULL
                : \Magento\Paypal\Model\Config::REFUND_TYPE_PARTIAL
            );
            $api->callRefundTransaction();
            $this->_importRefundResultToPayment($api, $payment, $canRefundMore);
        } else {
            \Mage::throwException(__('We can\'t issue a refund transaction because there is no capture transaction.'));
        }
    }

    /**
     * Cancel payment
     *
     * @param \Magento\Object $payment
     */
    public function cancel(\Magento\Object $payment)
    {
        if (!$payment->getOrder()->getInvoiceCollection()->count()) {
            $this->void($payment);
        }
    }

    /**
     * Check whether can do payment review
     *
     * @param \Magento\Payment\Model\Info $payment
     * @return bool
     */
    public function canReviewPayment(\Magento\Payment\Model\Info $payment)
    {
        $pendingReason = $payment->getAdditionalInformation(\Magento\Paypal\Model\Info::PENDING_REASON_GLOBAL);
        return $this->_isPaymentReviewRequired($payment)
            && $pendingReason != \Magento\Paypal\Model\Info::PAYMENTSTATUS_REVIEW;
    }

    /**
     * Check whether payment review is required
     *
     * @param \Magento\Payment\Model\Info $payment
     * @return bool
     */
    protected function _isPaymentReviewRequired(\Magento\Payment\Model\Info $payment)
    {
        return \Magento\Paypal\Model\Info::isPaymentReviewRequired($payment);
    }

    /**
     * Perform the payment review
     *
     * @param \Magento\Payment\Model\Info $payment
     * @param string $action
     * @return bool
     */
    public function reviewPayment(\Magento\Payment\Model\Info $payment, $action)
    {
        $api = $this->getApi()->setTransactionId($payment->getLastTransId());

        // check whether the review is still needed
        $api->callGetTransactionDetails();
        $this->importPaymentInfo($api, $payment);
        if (!$this->getInfo()->isPaymentReviewRequired($payment)) {
            return false;
        }

        // perform the review action
        $api->setAction($action)->callManagePendingTransactionStatus();
        $api->callGetTransactionDetails();
        $this->importPaymentInfo($api, $payment);
        return true;
    }

    /**
     * Fetch transaction details info
     *
     * @param \Magento\Payment\Model\Info $payment
     * @param string $transactionId
     * @return array
     */
    public function fetchTransactionInfo(\Magento\Payment\Model\Info $payment, $transactionId)
    {
        $api = $this->getApi()
            ->setTransactionId($transactionId)
            ->setRawResponseNeeded(true);
        $api->callGetTransactionDetails();
        $this->importPaymentInfo($api, $payment);
        $data = $api->getRawSuccessResponseData();
        return ($data) ? $data : array();
    }

    /**
     * Validate RP data
     *
     * @param \Magento\Payment\Model\Recurring\Profile $profile
     * @throws \Magento\Core\Exception
     */
    public function validateRecurringProfile(\Magento\Payment\Model\Recurring\Profile $profile)
    {
        $errors = array();
        if (strlen($profile->getSubscriberName()) > 32) { // up to 32 single-byte chars
            $errors[] = __('The subscriber name is too long.');
        }
        $refId = $profile->getInternalReferenceId(); // up to 127 single-byte alphanumeric
        if (strlen($refId) > 127) { //  || !preg_match('/^[a-z\d\s]+$/i', $refId)
            $errors[] = __('The merchant\'s reference ID format is not supported.');
        }
        $scheduleDescr = $profile->getScheduleDescription(); // up to 127 single-byte alphanumeric
        if (strlen($refId) > 127) { //  || !preg_match('/^[a-z\d\s]+$/i', $scheduleDescr)
            $errors[] = __('The schedule description is too long.');
        }
        if ($errors) {
            \Mage::throwException(implode(' ', $errors));
        }
    }

    /**
     * Submit RP to the gateway
     *
     * @param \Magento\Payment\Model\Recurring\Profile $profile
     * @param \Magento\Payment\Model\Info $paymentInfo
     * @throws \Magento\Core\Exception
     */
    public function submitRecurringProfile(\Magento\Payment\Model\Recurring\Profile $profile,
        \Magento\Payment\Model\Info $paymentInfo
    ) {
        $api = $this->getApi();
        \Magento\Object\Mapper::accumulateByMap($profile, $api, array(
            'token', // EC fields
            // TODO: DP fields
            // profile fields
            'subscriber_name', 'start_datetime', 'internal_reference_id', 'schedule_description',
            'suspension_threshold', 'bill_failed_later', 'period_unit', 'period_frequency', 'period_max_cycles',
            'billing_amount' => 'amount', 'trial_period_unit', 'trial_period_frequency', 'trial_period_max_cycles',
            'trial_billing_amount', 'currency_code', 'shipping_amount', 'tax_amount', 'init_amount', 'init_may_fail',
        ));
        $api->callCreateRecurringPaymentsProfile();
        $profile->setReferenceId($api->getRecurringProfileId());
        if ($api->getIsProfileActive()) {
            $profile->setState(\Magento\Sales\Model\Recurring\Profile::STATE_ACTIVE);
        } elseif ($api->getIsProfilePending()) {
            $profile->setState(\Magento\Sales\Model\Recurring\Profile::STATE_PENDING);
        }
    }

    /**
     * Fetch RP details
     *
     * @param string $referenceId
     * @param \Magento\Object $result
     */
    public function getRecurringProfileDetails($referenceId, \Magento\Object $result)
    {
        $api = $this->getApi();
        $api->setRecurringProfileId($referenceId)
            ->callGetRecurringPaymentsProfileDetails($result)
        ;
    }

    /**
     * Update RP data
     *
     * @param \Magento\Payment\Model\Recurring\Profile $profile
     */
    public function updateRecurringProfile(\Magento\Payment\Model\Recurring\Profile $profile)
    {

    }

    /**
     * Manage status
     *
     * @param \Magento\Payment\Model\Recurring\Profile $profile
     */
    public function updateRecurringProfileStatus(\Magento\Payment\Model\Recurring\Profile $profile)
    {
        $api = $this->getApi();
        $action = null;
        switch ($profile->getNewState()) {
            case \Magento\Sales\Model\Recurring\Profile::STATE_CANCELED: $action = 'cancel'; break;
            case \Magento\Sales\Model\Recurring\Profile::STATE_SUSPENDED: $action = 'suspend'; break;
            case \Magento\Sales\Model\Recurring\Profile::STATE_ACTIVE: $action = 'activate'; break;
        }
        $state = $profile->getState();
        $api->setRecurringProfileId($profile->getReferenceId())
            ->setIsAlreadyCanceled($state == \Magento\Sales\Model\Recurring\Profile::STATE_CANCELED)
            ->setIsAlreadySuspended($state == \Magento\Sales\Model\Recurring\Profile::STATE_SUSPENDED)
            ->setIsAlreadyActive($state == \Magento\Sales\Model\Recurring\Profile::STATE_ACTIVE)
            ->setAction($action)
            ->callManageRecurringPaymentsProfileStatus()
        ;
    }

    /**
     * Import capture results to payment
     *
     * @param \Magento\Paypal\Model\Api\Nvp
     * @param \Magento\Sales\Model\Order\Payment
     */
    protected function _importCaptureResultToPayment($api, $payment)
    {
        $payment->setTransactionId($api->getTransactionId())->setIsTransactionClosed(false);
        $this->importPaymentInfo($api, $payment);
    }

    /**
     * Import refund results to payment
     *
     * @param \Magento\Paypal\Model\Api\Nvp
     * @param \Magento\Sales\Model\Order\Payment
     * @param bool $canRefundMore
     */
    protected function _importRefundResultToPayment($api, $payment, $canRefundMore)
    {
        $payment->setTransactionId($api->getRefundTransactionId())
                ->setIsTransactionClosed(1) // refund initiated by merchant
                ->setShouldCloseParentTransaction(!$canRefundMore)
            ;
        $this->importPaymentInfo($api, $payment);
    }

    /**
     * Parent transaction id getter
     *
     * @param \Magento\Object $payment
     * @return string
     */
    protected function _getParentTransactionId(\Magento\Object $payment)
    {
        return $payment->getParentTransactionId();
    }
}
