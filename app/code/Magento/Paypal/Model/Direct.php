<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Model;

use Magento\Sales\Model\Order\Payment;

/**
 * PayPal Direct Module
 */
class Direct extends \Magento\Payment\Model\Method\Cc
{
    /**
     * @var string
     */
    protected $_code  = \Magento\Paypal\Model\Config::METHOD_WPP_DIRECT;

    /**
     * @var string
     */
    protected $_infoBlockType = 'Magento\Paypal\Block\Payment\Info';

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_isGateway               = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canAuthorize            = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canCapture              = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canCapturePartial       = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canRefund               = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canRefundInvoicePartial = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canVoid                 = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canUseInternal          = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canUseCheckout          = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canSaveCc = false;

    /**
     * @var bool
     */
    protected $_canFetchTransactionInfo = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canReviewPayment        = true;

    /**
     * Website Payments Pro instance
     *
     * @var \Magento\Paypal\Model\Pro
     */
    protected $_pro;

    /**
     * Website Payments Pro instance type
     *
     * @var $_proType string
     */
    protected $_proType = 'Magento\Paypal\Model\Pro';

    /**
     * @var \Magento\Paypal\Model\Method\ProTypeFactory
     */
    protected $_proTypeFactory;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\App\RequestInterface
     */
    protected $_requestHttp;

    /**
     * @var \Magento\Paypal\Model\CartFactory
     */
    protected $_cartFactory;

    /**
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Logger\AdapterFactory $logAdapterFactory
     * @param \Magento\Logger $logger
     * @param \Magento\Module\ModuleListInterface $moduleList
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Centinel\Model\Service $centinelService
     * @param \Magento\Paypal\Model\Method\ProTypeFactory $proTypeFactory
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\UrlInterface $urlBuilder
     * @param \Magento\App\RequestInterface $requestHttp
     * @param \Magento\Paypal\Model\CartFactory $cartFactory
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Logger\AdapterFactory $logAdapterFactory,
        \Magento\Logger $logger,
        \Magento\Module\ModuleListInterface $moduleList,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Centinel\Model\Service $centinelService,
        \Magento\Paypal\Model\Method\ProTypeFactory $proTypeFactory,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\UrlInterface $urlBuilder,
        \Magento\App\RequestInterface $requestHttp,
        \Magento\Paypal\Model\CartFactory $cartFactory,
        array $data = array()
    ) {
        parent::__construct(
            $eventManager,
            $paymentData,
            $coreStoreConfig,
            $logAdapterFactory,
            $logger,
            $moduleList,
            $locale,
            $centinelService,
            $data
        );
        $this->_proTypeFactory = $proTypeFactory;
        $this->_storeManager = $storeManager;
        $this->_urlBuilder = $urlBuilder;
        $this->_requestHttp = $requestHttp;
        $this->_cartFactory = $cartFactory;

        $proInstance = array_shift($data);
        if ($proInstance && ($proInstance instanceof \Magento\Paypal\Model\Pro)) {
            $this->_pro = $proInstance;
        } else {
            $this->_pro = $this->_proTypeFactory->create($this->_proType);
        }
        $this->_pro->setMethod($this->_code);
    }

    /**
     * Store setter
     * Also updates store ID in config object
     *
     * @param \Magento\Core\Model\Store|int $store
     * @return $this
     */
    public function setStore($store)
    {
        $this->setData('store', $store);
        if (null === $store) {
            $store = $this->_storeManager->getStore()->getId();
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
     * @see \Magento\Sales\Model\Payment::place()
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
     *
     * @param \Magento\Sales\Model\Quote|null $quote
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
     * @param int|null $storeId
     * @return null|string
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
     * @param \Magento\Object|Payment $payment
     * @param float $amount
     * @return $this
     */
    public function authorize(\Magento\Object $payment, $amount)
    {
        return $this->_placeOrder($payment, $amount);
    }

    /**
     * Void payment
     *
     * @param \Magento\Object|Payment $payment
     * @return $this
     */
    public function void(\Magento\Object $payment)
    {
        $this->_pro->void($payment);
        return $this;
    }

    /**
     * Capture payment
     *
     * @param \Magento\Object|Payment $payment
     * @param float $amount
     * @return $this
     */
    public function capture(\Magento\Object $payment, $amount)
    {
        if (false === $this->_pro->capture($payment, $amount)) {
            $this->_placeOrder($payment, $amount);
        }
        return $this;
    }

    /**
     * Refund capture
     *
     * @param \Magento\Object|Payment $payment
     * @param float $amount
     * @return $this
     */
    public function refund(\Magento\Object $payment, $amount)
    {
        $this->_pro->refund($payment, $amount);
        return $this;
    }

    /**
     * Cancel payment
     *
     * @param \Magento\Object|Payment $payment
     * @return $this
     */
    public function cancel(\Magento\Object $payment)
    {
        $this->void($payment);

        return $this;
    }

    /**
     * Whether payment can be reviewed
     *
     * @param \Magento\Payment\Model\Info|Payment $payment
     * @return bool
     */
    public function canReviewPayment(\Magento\Payment\Model\Info $payment)
    {
        return parent::canReviewPayment($payment) && $this->_pro->canReviewPayment($payment);
    }

    /**
     * Attempt to accept a pending payment
     *
     * @param \Magento\Payment\Model\Info|Payment $payment
     * @return bool
     */
    public function acceptPayment(\Magento\Payment\Model\Info $payment)
    {
        parent::acceptPayment($payment);
        return $this->_pro->reviewPayment($payment, \Magento\Paypal\Model\Pro::PAYMENT_REVIEW_ACCEPT);
    }

    /**
     * Attempt to deny a pending payment
     *
     * @param \Magento\Payment\Model\Info|Payment $payment
     * @return bool
     */
    public function denyPayment(\Magento\Payment\Model\Info $payment)
    {
        parent::denyPayment($payment);
        return $this->_pro->reviewPayment($payment, \Magento\Paypal\Model\Pro::PAYMENT_REVIEW_DENY);
    }

    /**
     * Set fallback API URL if not defined in configuration
     *
     * @return \Magento\Centinel\Model\Service
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
     * @param \Magento\Payment\Model\Info $payment
     * @param string $transactionId
     * @return array
     */
    public function fetchTransactionInfo(\Magento\Payment\Model\Info $payment, $transactionId)
    {
        return $this->_pro->fetchTransactionInfo($payment, $transactionId);
    }

    /**
     * Place an order with authorization or capture action
     *
     * @param Payment $payment
     * @param float $amount
     * @return $this
     */
    protected function _placeOrder(Payment $payment, $amount)
    {
        $order = $payment->getOrder();
        $api = $this->_pro->getApi()
            ->setPaymentAction($this->_pro->getConfig()->paymentAction)
            ->setIpAddress($this->_requestHttp->getClientIp(false))
            ->setAmount($amount)
            ->setCurrencyCode($order->getBaseCurrencyCode())
            ->setInvNum($order->getIncrementId())
            ->setEmail($order->getCustomerEmail())
            ->setNotifyUrl($this->_urlBuilder->getUrl('paypal/ipn/'))
            ->setCreditCardType($payment->getCcType())
            ->setCreditCardNumber($payment->getCcNumber())
            ->setCreditCardExpirationDate(
                $this->_getFormattedCcExpirationDate($payment->getCcExpMonth(), $payment->getCcExpYear())
            )
            ->setCreditCardCvv2($payment->getCcCid())
            ->setMaestroSoloIssueNumber($payment->getCcSsIssue());

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
        $cart = $this->_cartFactory->create(array('salesModel' => $order));

        $api->setPaypalCart($cart)
            ->setIsLineItemsEnabled($this->_pro->getConfig()->lineItemsEnabled);

        // call api and import transaction and other payment information
        $api->callDoDirectPayment();
        $this->_importResultToPayment($api, $payment);

        try {
            $api->callGetTransactionDetails();
        } catch (\Magento\Core\Exception $e) {
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
     * @param \Magento\Paypal\Model\Api\Nvp $api
     * @param Payment $payment
     * @return void
     */
    protected function _importResultToPayment($api, $payment)
    {
        $payment->setTransactionId($api->getTransactionId())->setIsTransactionClosed(0);
        $this->_pro->importPaymentInfo($api, $payment);
    }

    /**
     * Check void availability
     *
     * @param \Magento\Object $payment
     * @return bool
     */
    public function canVoid(\Magento\Object $payment)
    {
        if ($payment instanceof \Magento\Sales\Model\Order\Invoice
            || $payment instanceof \Magento\Sales\Model\Order\Creditmemo
        ) {
            return false;
        }

        return $this->_canVoid;
    }
}
