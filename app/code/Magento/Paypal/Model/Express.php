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
 * PayPal Express Module
 */
class Magento_Paypal_Model_Express extends Magento_Payment_Model_Method_Abstract
    implements Magento_Payment_Model_Recurring_Profile_MethodInterface
{
    protected $_code  = Magento_Paypal_Model_Config::METHOD_WPP_EXPRESS;
    protected $_formBlockType = 'Magento_Paypal_Block_Express_Form';
    protected $_infoBlockType = 'Magento_Paypal_Block_Payment_Info';

    /**
     * Website Payments Pro instance type
     *
     * @var $_proType string
     */
    protected $_proType = 'Magento_Paypal_Model_Pro';

    /**
     * Availability options
     */
    protected $_isGateway                   = false;
    protected $_canOrder                    = true;
    protected $_canAuthorize                = true;
    protected $_canCapture                  = true;
    protected $_canCapturePartial           = true;
    protected $_canRefund                   = true;
    protected $_canRefundInvoicePartial     = true;
    protected $_canVoid                     = true;
    protected $_canUseInternal              = false;
    protected $_canUseCheckout              = true;
    protected $_canUseForMultishipping      = false;
    protected $_canFetchTransactionInfo     = true;
    protected $_canCreateBillingAgreement   = true;
    protected $_canReviewPayment            = true;

    /**
     * Website Payments Pro instance
     *
     * @var Magento_Paypal_Model_Pro
     */
    protected $_pro = null;

    /**
     * Payment additional information key for payment action
     * @var string
     */
    protected $_isOrderPaymentActionKey = 'is_order_action';

    /**
     * Payment additional information key for number of used authorizations
     *
     * @var string
     */
    protected $_authorizationCountKey = 'authorization_count';

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Payment_Helper_Data $paymentData
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Payment_Helper_Data $paymentData,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        array $data = array()
    ) {
        parent::__construct($eventManager, $paymentData, $coreStoreConfig, $data);
        $proInstance = array_shift($data);
        if ($proInstance && ($proInstance instanceof Magento_Paypal_Model_Pro)) {
            $this->_pro = $proInstance;
        } else {
            $this->_pro = Mage::getModel($this->_proType);
        }
        $this->_pro->setMethod($this->_code);
    }

    /**
     * Store setter
     * Also updates store ID in config object
     *
     * @param Magento_Core_Model_Store|int $store
     */
    public function setStore($store)
    {
        $this->setData('store', $store);
        if (null === $store) {
            $store = Mage::app()->getStore()->getId();
        }
        $this->_pro->getConfig()->setStoreId(is_object($store) ? $store->getId() : $store);
        return $this;
    }

   /**
    * Can be used in regular checkout
    *
    * @return bool
    */
   public function canUseCheckout()
   {
       if ($this->_coreStoreConfig->getConfigFlag('payment/hosted_pro/active')
           && !$this->_coreStoreConfig->getConfigFlag('payment/hosted_pro/display_ec')
       ) {
           return false;
       }
       return parent::canUseCheckout();
   }

    /**
     * Whether method is available for specified currency
     *
     * @param string $currencyCode
     * @return bool
     */
    public function canUseForCurrency($currencyCode)
    {
        return $this->_pro->getConfig()->isCurrencyCodeSupported($currencyCode);
    }

    /**
     * Payment action getter compatible with payment model
     *
     * @see Magento_Sales_Model_Payment::place()
     * @return string
     */
    public function getConfigPaymentAction()
    {
        return $this->_pro->getConfig()->getPaymentAction();
    }

    /**
     * Check whether payment method can be used
     * @param Magento_Sales_Model_Quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        if (parent::isAvailable($quote) && $this->_pro->getConfig()->isMethodAvailable()) {
            return true;
        }
        return false;
    }

    /**
     * Custom getter for payment configuration
     *
     * @param string $field
     * @param int $storeId
     * @return mixed
     */
    public function getConfigData($field, $storeId = null)
    {
        return $this->_pro->getConfig()->$field;
    }

    /**
     * Order payment
     *
     * @param Magento_Sales_Model_Order_Payment $payment
     * @param float $amount
     * @return Magento_Paypal_Model_Express
     */
    public function order(Magento_Object $payment, $amount)
    {
        $this->_placeOrder($payment, $amount);

        $payment->setAdditionalInformation($this->_isOrderPaymentActionKey, true);

        if ($payment->getIsFraudDetected()) {
            return $this;
        }

        $order = $payment->getOrder();
        $orderTransactionId = $payment->getTransactionId();

        $api = $this->_callDoAuthorize($amount, $payment, $payment->getTransactionId());

        $state  = Magento_Sales_Model_Order::STATE_PROCESSING;
        $status = true;

        $formatedPrice = $order->getBaseCurrency()->formatTxt($amount);
        if ($payment->getIsTransactionPending()) {
            $message = __('The ordering amount of %1 is pending approval on the payment gateway.', $formatedPrice);
            $state = Magento_Sales_Model_Order::STATE_PAYMENT_REVIEW;
        } else {
            $message = __('Ordered amount of %1', $formatedPrice);
        }

        $payment->addTransaction(Magento_Sales_Model_Order_Payment_Transaction::TYPE_ORDER, null, false, $message);

        $this->_pro->importPaymentInfo($api, $payment);

        if ($payment->getIsTransactionPending()) {
            $message = __('We\'ll authorize the amount of %1 as soon as the payment gateway approves it.', $formatedPrice);
            $state = Magento_Sales_Model_Order::STATE_PAYMENT_REVIEW;
            if ($payment->getIsFraudDetected()) {
                $status = Magento_Sales_Model_Order::STATUS_FRAUD;
            }
        } else {
            $message = __('The authorized amount is %1.', $formatedPrice);
        }

        $payment->resetTransactionAdditionalInfo();

        $payment->setTransactionId($api->getTransactionId());
        $payment->setParentTransactionId($orderTransactionId);

        $transaction = $payment->addTransaction(Magento_Sales_Model_Order_Payment_Transaction::TYPE_AUTH, null, false,
            $message
        );

        $order->setState($state, $status);

        $payment->setSkipOrderProcessing(true);
        return $this;
    }

    /**
     * Authorize payment
     *
     * @param Magento_Sales_Model_Order_Payment $payment
     * @param float $amount
     * @return Magento_Paypal_Model_Express
     */
    public function authorize(Magento_Object $payment, $amount)
    {
        return $this->_placeOrder($payment, $amount);
    }

    /**
     * Void payment
     *
     * @param Magento_Sales_Model_Order_Payment $payment
     * @return Magento_Paypal_Model_Express
     */
    public function void(Magento_Object $payment)
    {
        //Switching to order transaction if needed
        if ($payment->getAdditionalInformation($this->_isOrderPaymentActionKey)
            && !$payment->getVoidOnlyAuthorization()
        ) {
            $orderTransaction = $payment->lookupTransaction(
                false, Magento_Sales_Model_Order_Payment_Transaction::TYPE_ORDER
            );
            if ($orderTransaction) {
                $payment->setParentTransactionId($orderTransaction->getTxnId());
                $payment->setTransactionId($orderTransaction->getTxnId() . '-void');
            }
        }
        $this->_pro->void($payment);
        return $this;
    }

    /**
     * Capture payment
     *
     * @param Magento_Sales_Model_Order_Payment $payment
     * @param float $amount
     * @return Magento_Paypal_Model_Express
     */
    public function capture(Magento_Object $payment, $amount)
    {
        $authorizationTransaction = $payment->getAuthorizationTransaction();
        $authorizationPeriod = abs(intval($this->getConfigData('authorization_honor_period')));
        $maxAuthorizationNumber = abs(intval($this->getConfigData('child_authorization_number')));
        $order = $payment->getOrder();
        $isAuthorizationCreated = false;

        if ($payment->getAdditionalInformation($this->_isOrderPaymentActionKey)) {
            $voided = false;
            if (!$authorizationTransaction->getIsClosed()
                && $this->_isTransactionExpired($authorizationTransaction, $authorizationPeriod)
            ) {
                //Save payment state and configure payment object for voiding
                $isCaptureFinal = $payment->getShouldCloseParentTransaction();
                $captureTrxId = $payment->getTransactionId();
                $payment->setShouldCloseParentTransaction(false);
                $payment->setParentTransactionId($authorizationTransaction->getTxnId());
                $payment->unsTransactionId();
                $payment->setVoidOnlyAuthorization(true);
                $payment->void(new Magento_Object());

                //Revert payment state after voiding
                $payment->unsAuthorizationTransaction();
                $payment->unsTransactionId();
                $payment->setShouldCloseParentTransaction($isCaptureFinal);
                $voided = true;
            }

            if ($authorizationTransaction->getIsClosed() || $voided) {
                if ($payment->getAdditionalInformation($this->_authorizationCountKey) > $maxAuthorizationNumber - 1) {
                    Mage::throwException(__('The maximum number of child authorizations is reached.'));
                }
                $api = $this->_callDoAuthorize(
                    $amount,
                    $payment,
                    $authorizationTransaction->getParentTxnId()
                );

                //Adding authorization transaction
                $this->_pro->importPaymentInfo($api, $payment);
                $payment->setTransactionId($api->getTransactionId());
                $payment->setParentTransactionId($authorizationTransaction->getParentTxnId());
                $payment->setIsTransactionClosed(false);

                $formatedPrice = $order->getBaseCurrency()->formatTxt($amount);

                if ($payment->getIsTransactionPending()) {
                    $message = __('We\'ll authorize the amount of %1 as soon as the payment gateway approves it.', $formatedPrice);
                } else {
                    $message = __('The authorized amount is %1.', $formatedPrice);
                }

                $transaction = $payment->addTransaction(Magento_Sales_Model_Order_Payment_Transaction::TYPE_AUTH, null,
                    true, $message
                );

                $payment->setParentTransactionId($api->getTransactionId());
                $isAuthorizationCreated = true;
            }
            //close order transaction if needed
            if ($payment->getShouldCloseParentTransaction()) {
                $orderTransaction = $payment->lookupTransaction(
                    false, Magento_Sales_Model_Order_Payment_Transaction::TYPE_ORDER
                );

                if ($orderTransaction) {
                    $orderTransaction->setIsClosed(true);
                    $order->addRelatedObject($orderTransaction);
                }
            }
        }

        if (false === $this->_pro->capture($payment, $amount)) {
            $this->_placeOrder($payment, $amount);
        }

        if ($isAuthorizationCreated && isset($transaction)) {
            $transaction->setIsClosed(true);
        }

        return $this;
    }

    /**
     * Refund capture
     *
     * @param Magento_Sales_Model_Order_Payment $payment
     * @param float $amount
     * @return Magento_Paypal_Model_Express
     */
    public function refund(Magento_Object $payment, $amount)
    {
        $this->_pro->refund($payment, $amount);
        return $this;
    }

    /**
     * Cancel payment
     *
     * @param Magento_Sales_Model_Order_Payment $payment
     * @return Magento_Paypal_Model_Express
     */
    public function cancel(Magento_Object $payment)
    {
        $this->void($payment);

        return $this;
    }

    /**
     * Whether payment can be reviewed
     *
     * @param Magento_Sales_Model_Order_Payment $payment
     * @return bool
     */
    public function canReviewPayment(Magento_Payment_Model_Info $payment)
    {
        return parent::canReviewPayment($payment) && $this->_pro->canReviewPayment($payment);
    }

    /**
     * Attempt to accept a pending payment
     *
     * @param Magento_Sales_Model_Order_Payment $payment
     * @return bool
     */
    public function acceptPayment(Magento_Payment_Model_Info $payment)
    {
        parent::acceptPayment($payment);
        return $this->_pro->reviewPayment($payment, Magento_Paypal_Model_Pro::PAYMENT_REVIEW_ACCEPT);
    }

    /**
     * Attempt to deny a pending payment
     *
     * @param Magento_Sales_Model_Order_Payment $payment
     * @return bool
     */
    public function denyPayment(Magento_Payment_Model_Info $payment)
    {
        parent::denyPayment($payment);
        return $this->_pro->reviewPayment($payment, Magento_Paypal_Model_Pro::PAYMENT_REVIEW_DENY);
    }

    /**
     * Checkout redirect URL getter for onepage checkout (hardcode)
     *
     * @see Magento_Checkout_Controller_Onepage::savePaymentAction()
     * @see Magento_Sales_Model_Quote_Payment::getCheckoutRedirectUrl()
     * @return string
     */
    public function getCheckoutRedirectUrl()
    {
        return Mage::getUrl('paypal/express/start');
    }

    /**
     * Fetch transaction details info
     *
     * @param Magento_Payment_Model_Info $payment
     * @param string $transactionId
     * @return array
     */
    public function fetchTransactionInfo(Magento_Payment_Model_Info $payment, $transactionId)
    {
        return $this->_pro->fetchTransactionInfo($payment, $transactionId);
    }

    /**
     * Validate RP data
     *
     * @param Magento_Payment_Model_Recurring_Profile $profile
     */
    public function validateRecurringProfile(Magento_Payment_Model_Recurring_Profile $profile)
    {
        return $this->_pro->validateRecurringProfile($profile);
    }

    /**
     * Submit RP to the gateway
     *
     * @param Magento_Payment_Model_Recurring_Profile $profile
     * @param Magento_Payment_Model_Info $paymentInfo
     */
    public function submitRecurringProfile(Magento_Payment_Model_Recurring_Profile $profile,
        Magento_Payment_Model_Info $paymentInfo
    ) {
        $token = $paymentInfo->
            getAdditionalInformation(Magento_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_TOKEN);
        $profile->setToken($token);
        $this->_pro->submitRecurringProfile($profile, $paymentInfo);
    }

    /**
     * Fetch RP details
     *
     * @param string $referenceId
     * @param Magento_Object $result
     */
    public function getRecurringProfileDetails($referenceId, Magento_Object $result)
    {
        return $this->_pro->getRecurringProfileDetails($referenceId, $result);
    }

    /**
     * Whether can get recurring profile details
     */
    public function canGetRecurringProfileDetails()
    {
        return true;
    }

    /**
     * Update RP data
     *
     * @param Magento_Payment_Model_Recurring_Profile $profile
     */
    public function updateRecurringProfile(Magento_Payment_Model_Recurring_Profile $profile)
    {
        return $this->_pro->updateRecurringProfile($profile);
    }

    /**
     * Manage status
     *
     * @param Magento_Payment_Model_Recurring_Profile $profile
     */
    public function updateRecurringProfileStatus(Magento_Payment_Model_Recurring_Profile $profile)
    {
        return $this->_pro->updateRecurringProfileStatus($profile);
    }

    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  Magento_Payment_Model_Info
     */
    public function assignData($data)
    {
        $result = parent::assignData($data);
        $key = Magento_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_BILLING_AGREEMENT;
        if (is_array($data)) {
            $this->getInfoInstance()->setAdditionalInformation($key, isset($data[$key]) ? $data[$key] : null);
        }
        elseif ($data instanceof Magento_Object) {
            $this->getInfoInstance()->setAdditionalInformation($key, $data->getData($key));
        }
        return $result;
    }

    /**
     * Place an order with authorization or capture action
     *
     * @param Magento_Sales_Model_Order_Payment $payment
     * @param float $amount
     * @return Magento_Paypal_Model_Express
     */
    protected function _placeOrder(Magento_Sales_Model_Order_Payment $payment, $amount)
    {
        $order = $payment->getOrder();

        // prepare api call
        $token = $payment->getAdditionalInformation(Magento_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_TOKEN);
        $parameters = array('params' => array($order));
        $api = $this->_pro->getApi()
            ->setToken($token)
            ->setPayerId($payment->
                getAdditionalInformation(Magento_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_PAYER_ID))
            ->setAmount($amount)
            ->setPaymentAction($this->_pro->getConfig()->paymentAction)
            ->setNotifyUrl(Mage::getUrl('paypal/ipn/'))
            ->setInvNum($order->getIncrementId())
            ->setCurrencyCode($order->getBaseCurrencyCode())
            ->setPaypalCart(Mage::getModel('Magento_Paypal_Model_Cart', $parameters))
            ->setIsLineItemsEnabled($this->_pro->getConfig()->lineItemsEnabled)
        ;
        if ($order->getIsVirtual()) {
            $api->setAddress($order->getBillingAddress())->setSuppressShipping(true);
        } else {
            $api->setAddress($order->getShippingAddress());
            $api->setBillingAddress($order->getBillingAddress());
        }

        // call api and get details from it
        $api->callDoExpressCheckoutPayment();

        $this->_importToPayment($api, $payment);
        return $this;
    }

    /**
     * Import payment info to payment
     *
     * @param Magento_Paypal_Model_Api_Nvp
     * @param Magento_Sales_Model_Order_Payment
     */
    protected function _importToPayment($api, $payment)
    {
        $payment->setTransactionId($api->getTransactionId())->setIsTransactionClosed(0)
            ->setAdditionalInformation(Magento_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_REDIRECT,
                $api->getRedirectRequired()
            );

        if ($api->getBillingAgreementId()) {
            $payment->setBillingAgreementData(array(
                'billing_agreement_id'  => $api->getBillingAgreementId(),
                'method_code'           => Magento_Paypal_Model_Config::METHOD_BILLING_AGREEMENT
            ));
        }

        $this->_pro->importPaymentInfo($api, $payment);
    }

    /**
     * Check void availability
     *
     * @param   Magento_Object $payment
     * @return  bool
     */
    public function canVoid(Magento_Object $payment)
    {
        if ($payment instanceof Magento_Sales_Model_Order_Invoice
            || $payment instanceof Magento_Sales_Model_Order_Creditmemo
        ) {
            return false;
        }
        $info = $this->getInfoInstance();
        if ($info->getAdditionalInformation($this->_isOrderPaymentActionKey)) {
            $orderTransaction = $info->lookupTransaction(
                false, Magento_Sales_Model_Order_Payment_Transaction::TYPE_ORDER
            );
            if ($orderTransaction) {
                $info->setParentTransactionId($orderTransaction->getTxnId());
            }
        }

        return $this->_canVoid;
    }

    /**
     * Check capture availability
     *
     * @return bool
     */
    public function canCapture()
    {
        $payment = $this->getInfoInstance();
        $this->_pro->getConfig()->setStoreId($payment->getOrder()->getStore()->getId());

        if ($payment->getAdditionalInformation($this->_isOrderPaymentActionKey)) {
            $orderTransaction = $payment->lookupTransaction(false,
                Magento_Sales_Model_Order_Payment_Transaction::TYPE_ORDER
            );
            if ($orderTransaction->getIsClosed()) {
                return false;
            }

            $orderValidPeriod = abs(intval($this->getConfigData('order_valid_period')));

            $dateCompass = new DateTime($orderTransaction->getCreatedAt());
            $dateCompass->modify('+' . $orderValidPeriod . ' days');
            $currentDate = new DateTime();

            if ($currentDate > $dateCompass || $orderValidPeriod == 0) {
                return false;
            }
        }
        return $this->_canCapture;
    }

    /**
     * Call DoAuthorize
     *
     * @param int $amount
     * @param Magento_Object $payment
     * @param string $parentTransactionId
     * @return Magento_Paypal_Model_Api_Abstract
     */
    protected function _callDoAuthorize($amount, $payment, $parentTransactionId)
    {
        $api = $this->_pro->resetApi()->getApi()
            ->setAmount($amount)
            ->setCurrencyCode($payment->getOrder()->getBaseCurrencyCode())
            ->setTransactionId($parentTransactionId)
            ->callDoAuthorization();

        $payment->setAdditionalInformation($this->_authorizationCountKey,
            $payment->getAdditionalInformation($this->_authorizationCountKey) + 1
        );

        return $api;
    }

    /**
     * Check transaction for expiration in PST
     *
     * @param Magento_Sales_Model_Order_Payment_Transaction $transaction
     * @param int $period
     * @return boolean
     */
    protected function _isTransactionExpired(Magento_Sales_Model_Order_Payment_Transaction $transaction, $period)
    {
        $period = intval($period);
        if (0 == $period) {
            return true;
        }

        $transactionClosingDate = new DateTime($transaction->getCreatedAt(), new DateTimeZone('GMT'));
        $transactionClosingDate->setTimezone(new DateTimeZone('US/Pacific'));
        /**
         * 11:49:00 PayPal transactions closing time
         */
        $transactionClosingDate->setTime(11, 49, 00);
        $transactionClosingDate->modify('+' . $period . ' days');

        $currentTime = new DateTime(null, new DateTimeZone('US/Pacific'));

        if ($currentTime > $transactionClosingDate) {
            return true;
        }

        return false;
    }
}
