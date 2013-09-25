<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * Payone dummy payment method model
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento
 */
class Magento_Pbridge_Model_Payment_Method_Payone_Gate extends Magento_Payment_Model_Method_Cc
{
    /**
     * Payone payment method code
     *
     * @var string
     */
    const PAYMENT_CODE = 'payone_gate';

    protected $_code = self::PAYMENT_CODE;

    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = true;
    protected $_canRefundInvoicePartial = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;
    protected $_canSaveCc               = false;

    protected $_allowCurrencyCode = array('EUR');

    /**
     * Form block type for the frontend
     *
     * @var string
     */
    protected $_formBlockType = 'Magento_Pbridge_Block_Checkout_Payment_Payone_Gate';

    /**
     * Form block type for the backend
     *
     * @var string
     */
    protected $_backendFormBlockType = 'Magento_Pbridge_Block_Adminhtml_Sales_Order_Create_Payone_Gate';

    /**
     * Payment Bridge Payment Method Instance
     *
     * @var Magento_Pbridge_Model_Payment_Method_Pbridge
     */
    protected $_pbridgeMethodInstance = null;

    /**
     * Pbridge data
     *
     * @var Magento_Pbridge_Helper_Data
     */
    protected $_pbridgeData = null;

    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Construct
     *
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_ModuleListInterface $moduleList
     * @param Magento_Payment_Helper_Data $paymentData
     * @param Magento_Core_Model_Log_AdapterFactory $logAdapterFactory
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Centinel_Model_Service $centinelService
     * @param Magento_Pbridge_Helper_Data $pbridgeData
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_ModuleListInterface $moduleList,
        Magento_Payment_Helper_Data $paymentData,
        Magento_Core_Model_Log_AdapterFactory $logAdapterFactory,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Centinel_Model_Service $centinelService,
        Magento_Pbridge_Helper_Data $pbridgeData,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        array $data = array()
    ) {
        $this->_pbridgeData = $pbridgeData;
        $this->_storeManager = $storeManager;
        parent::__construct($logger, $eventManager, $coreStoreConfig, $moduleList, $paymentData, $logAdapterFactory,
            $locale, $centinelService, $data);
    }

    /**
     * Check method for processing with base currency
     *
     * @param string $currencyCode
     * @return boolean
     */
    public function canUseForCurrency($currencyCode)
    {
        if (!in_array($currencyCode, $this->getAcceptedCurrencyCodes())) {
            return false;
        }
        return true;
    }

    /**
     * Return array of currency codes supplied by Payment Gateway
     *
     * @return array
     */
    public function getAcceptedCurrencyCodes()
    {
        if (!$this->hasData('_accepted_currency')) {
            $acceptedCurrencyCodes = $this->_allowCurrencyCode;
            $acceptedCurrencyCodes[] = $this->getConfigData('currency');
            $this->setData('_accepted_currency', $acceptedCurrencyCodes);
        }
        return $this->_getData('_accepted_currency');
    }

    /**
     * Return that current payment method is dummy
     *
     * @return boolean
     */
    public function getIsDummy()
    {
        return true;
    }

    /**
     * Return Payment Bridge method instance
     *
     * @return Magento_Pbridge_Model_Payment_Method_Pbridge
     */
    public function getPbridgeMethodInstance()
    {
        if ($this->_pbridgeMethodInstance === null) {
            $this->_pbridgeMethodInstance = $this->_paymentData->getMethodInstance('pbridge');
            if ($this->_pbridgeMethodInstance) {
                $this->_pbridgeMethodInstance->setOriginalMethodInstance($this);
            }
        }
        return $this->_pbridgeMethodInstance;
    }

    /**
     * Retrieve original payment method code
     *
     * @return string
     */
    public function getOriginalCode()
    {
        return parent::getCode();
    }

    /**
     * Assign data to info model instance
     *
     * @param  mixed $data
     * @return Magento_Payment_Model_Info
     */
    public function assignData($data)
    {
        $this->getPbridgeMethodInstance()->assignData($data);
        return $this;
    }

    /**
     * Retrieve information from payment configuration
     *
     * @param   string $field
     * @param null $storeId
     * @return  mixed
     */
    public function getConfigData($field, $storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->getStore();
        }
        $path = 'payment/'.$this->getOriginalCode().'/'.$field;
        return $this->_coreStoreConfig->getConfig($path, $storeId);
    }

    /**
     * Check whether payment method can be used
     *
     * @param Magento_Sales_Model_Quote $quote
     * @return boolean
     */
    public function isAvailable($quote = null)
    {
        return $this->_pbridgeData->isEnabled($quote ? $quote->getStoreId() : null)
            && Magento_Payment_Model_Method_Abstract::isAvailable($quote);
    }

    /**
     * Retrieve block type for method form generation
     *
     * @return string
     */
    public function getFormBlockType()
    {
        return $this->_storeManager->getStore()->isAdmin() ?
            $this->_backendFormBlockType :
            $this->_formBlockType;
    }

    /**
     * Validate payment method information object
     *
     * @return Magento_Pbridge_Model_Payment_Method_Authorizenet
     */
    public function validate()
    {
        $this->getPbridgeMethodInstance()->validate();
        return $this;
    }

    /**
     * Authorization method being executed via Payment Bridge
     *
     * @param Magento_Object $payment
     * @param float $amount
     * @return Magento_Pbridge_Model_Payment_Method_Authorizenet
     */
    public function authorize(Magento_Object $payment, $amount)
    {
        $response = $this->getPbridgeMethodInstance()->authorize($payment, $amount);
        $payment->addData((array)$response);
        return $this;
    }

    /**
     * Capturing method being executed via Payment Bridge
     *
     * @param Magento_Object $payment
     * @param float $amount
     * @return Magento_Pbridge_Model_Payment_Method_Authorizenet
     */
    public function capture(Magento_Object $payment, $amount)
    {
        $response = $this->getPbridgeMethodInstance()->capture($payment, $amount);
        if (!$response) {
            $response = $this->getPbridgeMethodInstance()->authorize($payment, $amount);
        }
        $payment->addData((array)$response);
        return $this;
    }

    /**
     * Refunding method being executed via Payment Bridge
     *
     * @param Magento_Object $payment
     * @param float $amount
     * @return Magento_Pbridge_Model_Payment_Method_Authorizenet
     */
    public function refund(Magento_Object $payment, $amount)
    {
        $response = $this->getPbridgeMethodInstance()->refund($payment, $amount);
        $payment->addData((array)$response);
        return $this;
    }

    /**
     * Voiding method being executed via Payment Bridge
     *
     * @param Magento_Object $payment
     * @return Magento_Pbridge_Model_Payment_Method_Authorizenet
     */
    public function void(Magento_Object $payment)
    {
        $response = $this->getPbridgeMethodInstance()->void($payment);
        $payment->addData((array)$response);
        return $this;
    }
    /**
     * Return payment method Centinel validation status
     *
     * @return bool
     */
    public function getIsCentinelValidationEnabled()
    {
        return false;
    }

    /**
     * Store id setter, also set storeId to helper
     *
     * @param int $store
     * @return \Magento_Pbridge_Model_Payment_Method_Payone_Gate
     */
    public function setStore($store)
    {
        $this->setData('store', $store);
        $this->_pbridgeData->setStoreId(is_object($store) ? $store->getId() : $store);
        return $this;
    }

    /**
     * Check refund availability
     *
     * @return bool
     */
    public function canRefund()
    {
        return $this->_canRefund;
    }

    /**
     * Set capture transaction ID to invoice for informational purposes
     * @param Magento_Sales_Model_Order_Invoice $invoice
     * @param Magento_Sales_Model_Order_Payment $payment
     * @return Magento_Payment_Model_Method_Abstract
     */
    public function processInvoice($invoice, $payment)
    {
        $invoice->setTransactionId($payment->getLastTransId());
        return $this;
    }
}
