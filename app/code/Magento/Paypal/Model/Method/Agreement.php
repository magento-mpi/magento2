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
 * Paypal Billing Agreement method
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Magento_Paypal_Model_Method_Agreement extends Magento_Sales_Model_Payment_Method_Billing_AgreementAbstract
    implements Magento_Payment_Model_Billing_Agreement_MethodInterface
{
    /**
     * Method code
     *
     * @var string
     */
    protected $_code = Magento_Paypal_Model_Config::METHOD_BILLING_AGREEMENT;

    /**
     * Method instance settings
     *
     */
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = true;
    protected $_canRefund               = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid                 = true;
    protected $_canUseCheckout          = false;
    protected $_canUseInternal          = false;
    protected $_canFetchTransactionInfo = true;
    protected $_canReviewPayment        = true;

    /**
     * Website Payments Pro instance
     *
     * @var Magento_Paypal_Model_Pro
     */
    protected $_pro = null;

    /**
     * Initialize Magento_Paypal_Model_Pro model
     *
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Store_Config $coreStoreConfig,
        array $data = array()
    ) {
        parent::__construct($coreStoreConfig, $data);
        $proInstance = array_shift($data);
        if ($proInstance && ($proInstance instanceof Magento_Paypal_Model_Pro)) {
            $this->_pro = $proInstance;
        } else {
            $this->_pro = Mage::getModel('Magento_Paypal_Model_Pro');
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
     * Init billing agreement
     *
     * @param Magento_Payment_Model_Billing_AgreementAbstract $agreement
     * @return Magento_Paypal_Model_Method_Agreement
     */
    public function initBillingAgreementToken(Magento_Payment_Model_Billing_AgreementAbstract $agreement)
    {
        $api = $this->_pro->getApi()
            ->setReturnUrl($agreement->getReturnUrl())
            ->setCancelUrl($agreement->getCancelUrl())
            ->setBillingType($this->_pro->getApi()->getBillingAgreementType());

        $api->callSetCustomerBillingAgreement();
        $agreement->setRedirectUrl(
            $this->_pro->getConfig()->getStartBillingAgreementUrl($api->getToken())
        );
        return $this;
    }

    /**
     * Retrieve billing agreement customer details by token
     *
     * @param Magento_Payment_Model_Billing_AgreementAbstract $agreement
     * @return array
     */
    public function getBillingAgreementTokenInfo(Magento_Payment_Model_Billing_AgreementAbstract $agreement)
    {
        $api = $this->_pro->getApi()
            ->setToken($agreement->getToken());
        $api->callGetBillingAgreementCustomerDetails();
        $responseData = array(
            'token'         => $api->getData('token'),
            'email'         => $api->getData('email'),
            'payer_id'      => $api->getData('payer_id'),
            'payer_status'  => $api->getData('payer_status')
        );
        $agreement->addData($responseData);
        return $responseData;
    }

    /**
     * Create billing agreement by token specified in request
     *
     * @param Magento_Payment_Model_Billing_AgreementAbstract $agreement
     * @return Magento_Paypal_Model_Method_Agreement
     */
    public function placeBillingAgreement(Magento_Payment_Model_Billing_AgreementAbstract $agreement)
    {
        $api = $this->_pro->getApi()
            ->setToken($agreement->getToken());
        $api->callCreateBillingAgreement();
        $agreement->setBillingAgreementId($api->getData('billing_agreement_id'));
        return $this;
    }

    /**
     * Update billing agreement status
     *
     * @param Magento_Payment_Model_Billing_AgreementAbstract $agreement
     * @return Magento_Paypal_Model_Method_Agreement
     */
    public function updateBillingAgreementStatus(Magento_Payment_Model_Billing_AgreementAbstract $agreement)
    {
        $targetStatus = $agreement->getStatus();
        $api = $this->_pro->getApi()
            ->setReferenceId($agreement->getReferenceId())
            ->setBillingAgreementStatus($targetStatus);
        try {
            $api->callUpdateBillingAgreement();
        } catch (Magento_Core_Exception $e) {
            // when BA was already canceled, just pretend that the operation succeeded
            if (!(Magento_Sales_Model_Billing_Agreement::STATUS_CANCELED == $targetStatus
                && $api->getIsBillingAgreementAlreadyCancelled())) {
                throw $e;
            }
        }
        return $this;
    }

    /**
     * Authorize payment
     *
     * @param Magento_Object $payment
     * @param float $amount
     * @return Magento_Paypal_Model_Method_Agreement
     */
    public function authorize(Magento_Object $payment, $amount)
    {
        return $this->_placeOrder($payment, $amount);
    }

    /**
     * Void payment
     *
     * @param Magento_Sales_Model_Order_Payment $payment
     * @return Magento_Paypal_Model_Method_Agreement
     */
    public function void(Magento_Object $payment)
    {
        $this->_pro->void($payment);
        return $this;
    }

    /**
     * Capture payment
     *
     * @param Magento_Sales_Model_Order_Payment $payment
     * @param float $amount
     * @return Magento_Paypal_Model_Method_Agreement
     */
    public function capture(Magento_Object $payment, $amount)
    {
        if (false === $this->_pro->capture($payment, $amount)) {
            $this->_placeOrder($payment, $amount);
        }
        return $this;
    }

    /**
     * Refund capture
     *
     * @param Magento_Sales_Model_Order_Payment $payment
     * @param float $amount
     * @return Magento_Paypal_Model_Method_Agreement
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
     * @return Magento_Paypal_Model_Method_Agreement
     */
    public function cancel(Magento_Object $payment)
    {
        $this->_pro->cancel($payment);
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
     * Place an order with authorization or capture action
     *
     * @param Magento_Sales_Model_Order_Payment $payment
     * @param float $amount
     * @return Magento_Paypal_Model_Method_Agreement
     */
    protected function _placeOrder(Magento_Sales_Model_Order_Payment $payment, $amount)
    {
        $order = $payment->getOrder();
        $billingAgreement = Mage::getModel('Magento_Sales_Model_Billing_Agreement')->load(
            $payment->getAdditionalInformation(
                Magento_Sales_Model_Payment_Method_Billing_AgreementAbstract::TRANSPORT_BILLING_AGREEMENT_ID
            )
        );

        $parameters = array('params' => array($order));
        $api = $this->_pro->getApi()
            ->setReferenceId($billingAgreement->getReferenceId())
            ->setPaymentAction($this->_pro->getConfig()->paymentAction)
            ->setAmount($amount)
            ->setNotifyUrl(Mage::getUrl('paypal/ipn/'))
            ->setPaypalCart(Mage::getModel('Magento_Paypal_Model_Cart', $parameters))
            ->setIsLineItemsEnabled($this->_pro->getConfig()->lineItemsEnabled)
            ->setInvNum($order->getIncrementId())
        ;

        // call api and import transaction and other payment information
        $api->callDoReferenceTransaction();
        $this->_pro->importPaymentInfo($api, $payment);
        $api->callGetTransactionDetails();
        $this->_pro->importPaymentInfo($api, $payment);

        $payment->setTransactionId($api->getTransactionId())
            ->setIsTransactionClosed(0);

        if ($api->getBillingAgreementId()) {
            $order->addRelatedObject($billingAgreement);
            $billingAgreement->setIsObjectChanged(true);
            $billingAgreement->addOrderRelation($order);
        }

        return $this;
    }


    protected function _isAvailable($quote)
    {
        return $this->_pro->getConfig()->isMethodAvailable($this->_code);
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

}
