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
 *
 * PayPal Direct Module
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Paypal_Model_Direct extends Magento_Payment_Model_Method_Cc
{
    protected $_code  = Magento_Paypal_Model_Config::METHOD_WPP_DIRECT;
    protected $_infoBlockType = 'Magento_Paypal_Block_Payment_Info';

    /**
     * Availability options
     */
    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = true;
    protected $_canRefund               = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc = false;
    protected $_canFetchTransactionInfo = true;
    protected $_canReviewPayment        = true;

    /**
     * Website Payments Pro instance
     *
     * @var Magento_Paypal_Model_Pro
     */
    protected $_pro = null;

    /**
     * Website Payments Pro instance type
     *
     * @var $_proType string
     */
    protected $_proType = 'Magento_Paypal_Model_Pro';

    /**
     * @param Magento_Core_Model_Event_Manager_Proxy $eventManager
     * @param Magento_Core_Model_ModuleListInterface $moduleList
     * @param Magento_Payment_Helper_Data $paymentData
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Event_Manager_Proxy $eventManager,
        Magento_Core_Model_ModuleListInterface $moduleList,
        Magento_Payment_Helper_Data $paymentData,
        array $data = array()
    ) {
        parent::__construct($eventManager, $moduleList, $paymentData, $data);
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
     * Return available CC types for gateway based on merchant country
     *
     * @return string
     */
    public function getAllowedCcTypes()
    {
        $ccTypes = explode(',', $this->_pro->getConfig()->cctypes);
        $country = $this->_pro->getConfig()->getMerchantCountry();

        if ($country == 'GB') {
            $ccTypes = array_intersect(array('SM', 'SO', 'MC', 'DI', 'VI'), $ccTypes);
        } elseif ($country == 'CA') {
            $ccTypes = array_intersect(array('MC', 'VI'), $ccTypes);
        }
        return implode(',', $ccTypes);
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
        $value = null;
        switch ($field)
        {
            case 'cctypes':
                $value = $this->getAllowedCcTypes();
                break;
            default:
                $value = $this->_pro->getConfig()->$field;
        }
        return $value;
    }

    /**
     * Authorize payment
     *
     * @param Magento_Sales_Model_Order_Payment $payment
     * @return Magento_Paypal_Model_Direct
     */
    public function authorize(Magento_Object $payment, $amount)
    {
        return $this->_placeOrder($payment, $amount);
    }

    /**
     * Void payment
     *
     * @param Magento_Sales_Model_Order_Payment $payment
     * @return Magento_Paypal_Model_Direct
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
     * @return Magento_Paypal_Model_Direct
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
     * @return Magento_Paypal_Model_Direct
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
     * @return Magento_Paypal_Model_Direct
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
     * Set fallback API URL if not defined in configuration
     *
     * @return Magento_Centinel_Model_Service
     */
    public function getCentinelValidator()
    {
        $validator = parent::getCentinelValidator();
        if (!$validator->getCustomApiEndpointUrl()) {
            $validator->setCustomApiEndpointUrl($this->_pro->getConfig()->centinelDefaultApiUrl);
        }
        return $validator;
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
     * @return Magento_Paypal_Model_Direct
     */
    protected function _placeOrder(Magento_Sales_Model_Order_Payment $payment, $amount)
    {
        $order = $payment->getOrder();
        $api = $this->_pro->getApi()
            ->setPaymentAction($this->_pro->getConfig()->paymentAction)
            ->setIpAddress(Mage::app()->getRequest()->getClientIp(false))
            ->setAmount($amount)
            ->setCurrencyCode($order->getBaseCurrencyCode())
            ->setInvNum($order->getIncrementId())
            ->setEmail($order->getCustomerEmail())
            ->setNotifyUrl(Mage::getUrl('paypal/ipn/'))
            ->setCreditCardType($payment->getCcType())
            ->setCreditCardNumber($payment->getCcNumber())
            ->setCreditCardExpirationDate(
                $this->_getFormattedCcExpirationDate($payment->getCcExpMonth(), $payment->getCcExpYear())
            )
            ->setCreditCardCvv2($payment->getCcCid())
            ->setMaestroSoloIssueNumber($payment->getCcSsIssue())
        ;
        if ($payment->getCcSsStartMonth() && $payment->getCcSsStartYear()) {
            $year = sprintf('%02d', substr($payment->getCcSsStartYear(), -2, 2));
            $api->setMaestroSoloIssueDate(
                $this->_getFormattedCcExpirationDate($payment->getCcSsStartMonth(), $year)
            );
        }
        if ($this->getIsCentinelValidationEnabled()) {
            $this->getCentinelValidator()->exportCmpiData($api);
        }

        // add shipping and billing addresses
        if ($order->getIsVirtual()) {
            $api->setAddress($order->getBillingAddress())->setSuppressShipping(true);
        } else {
            $api->setAddress($order->getShippingAddress());
            $api->setBillingAddress($order->getBillingAddress());
        }

        // add line items
        $parameters = array('params' => array($order));
        $api->setPaypalCart(Mage::getModel('Magento_Paypal_Model_Cart', $parameters))
            ->setIsLineItemsEnabled($this->_pro->getConfig()->lineItemsEnabled)
        ;

        // call api and import transaction and other payment information
        $api->callDoDirectPayment();
        $this->_importResultToPayment($api, $payment);

        try {
            $api->callGetTransactionDetails();
        } catch (Magento_Core_Exception $e) {
            // if we receive errors, but DoDirectPayment response is Success, then set Pending status for transaction
            $payment->setIsTransactionPending(true);
        }
        $this->_importResultToPayment($api, $payment);
        return $this;
    }

    /**
     * Format credit card expiration date based on month and year values
     * Format: mmyyyy
     *
     * @param string|int $month
     * @param string|int $year
     * @return string
     */
    protected function _getFormattedCcExpirationDate($month, $year)
    {
        return sprintf('%02d%02d', $month, $year);
    }

    /**
     * Import direct payment results to payment
     *
     * @param Magento_Paypal_Model_Api_Nvp
     * @param Magento_Sales_Model_Order_Payment
     */
    protected function _importResultToPayment($api, $payment)
    {
        $payment->setTransactionId($api->getTransactionId())->setIsTransactionClosed(0);
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

        return $this->_canVoid;
    }
}
