<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sagepay Direct dummy payment method model
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Pbridge_Model_Payment_Method_Sagepay_Direct extends Magento_Payment_Model_Method_Cc
{
    /**
     * Payment code
     * @var string
     */
    protected $_code  = 'sagepay_direct';

    /**
     * Payment options
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
    protected $_canSaveCc               = false;
    protected $_isInitializeNeeded      = false;

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
     * Disable payment method if 3D Secure is enabled
     * @return bool
     */
    public function canUseForMultishipping()
    {
        if ($this->_is3DSEnabled()) {
            return false;
        }
        return parent::canUseForMultishipping();
    }

    /**
     * Form block type for the frontend
     * @var string
     */
    protected $_formBlockType = 'Magento_Pbridge_Block_Checkout_Payment_Sagepay_Direct';

    /**
     * Form block type for the backend
     * @var string
     */
    protected $_backendFormBlockType = 'Magento_Pbridge_Block_Adminhtml_Sales_Order_Create_Sagepay_Direct';

    /**
     * Payment Bridge Payment Method Instance
     * @var Magento_Pbridge_Model_Payment_Method_Pbridge
     */
    protected $_pbridgeMethodInstance = null;



    /**
     * Return that current payment method is dummy
     * @return boolean
     */
    public function getIsDummy()
    {
        return true;
    }

    /**
     * Retrieve dummy payment method code
     *
     * @return string
     */
    public function getCode()
    {
        return parent::getCode();
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

    public function getTitle()
    {
        return parent::getTitle();
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
     * Return configuration value of original payment
     *
     * @param string $field
     * @param int $storeId
     * @return mixed
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
     * Return Payment Bridge method instance
     * @return Magento_Pbridge_Model_Payment_Method_Pbridge
     */
    public function getPbridgeMethodInstance()
    {
        if ($this->_pbridgeMethodInstance === null) {
            $this->_pbridgeMethodInstance = $this->_paymentData->getMethodInstance('pbridge');
            $this->_pbridgeMethodInstance->setOriginalMethodInstance($this);
        }
        return $this->_pbridgeMethodInstance;
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
     * Validate payment method information object
     * @return Magento_Pbridge_Model_Payment_Method_Sagepay_Direct
     */
    public function validate()
    {
        $this->getPbridgeMethodInstance()->validate();
        return $this;
    }

    /**
     * Check whether payment method can be used
     *
     * @param Magento_Sales_Model_Quote $quote
     * @return boolean
     */
    public function isAvailable($quote = null)
    {
        return $this->getPbridgeMethodInstance() ?
            $this->getPbridgeMethodInstance()->isDummyMethodAvailable($quote) : false;
    }

    /**
     * Authorization method being executed via Payment Bridge
     *
     * @param Magento_Object $payment
     * @param float $amount
     * @return Magento_Pbridge_Model_Payment_Method_Sagepay_Direct
     */
    public function authorize(Magento_Object $payment, $amount)
    {
        $response = $this->getPbridgeMethodInstance()->authorize($payment, $amount);
        $payment->addData((array)$response);
        $payment->setIsTransactionClosed(0);
        return $this;
    }

    /**
     * Capturing method being executed via Payment Bridge
     *
     * @param Magento_Object $payment
     * @param float $amount
     * @return Magento_Pbridge_Model_Payment_Method_Sagepay_Direct
     */
    public function capture(Magento_Object $payment, $amount)
    {
        $response = $this->getPbridgeMethodInstance()->capture($payment, $amount);
        if (!$response) {
            $response = $this->getPbridgeMethodInstance()->authorize($payment, $amount);
        }
        $payment->addData((array)$response);
        $payment->setIsTransactionClosed(0);
        return $this;
    }

    /**
     * Refunding method being executed via Payment Bridge
     *
     * @param Magento_Object $payment
     * @param float $amount
     * @return Magento_Pbridge_Model_Payment_Method_Sagepay_Direct
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
     * @return Magento_Pbridge_Model_Payment_Method_Sagepay_Direct
     */
    public function void(Magento_Object $payment)
    {
        $response = $this->getPbridgeMethodInstance()->void($payment);
        $payment->addData((array)$response);
        return $this;
    }

    /**
     * Cancel payment
     *
     * @param Magento_Object $payment
     * @return Magento_Pbridge_Model_Payment_Method_Sagepay_Direct
     */
    public function cancel(Magento_Object $payment)
    {
        if (!$payment->getOrder()->getInvoiceCollection()->count()) {
            $response = $this->getPbridgeMethodInstance()->void($payment);
            $payment->addData((array)$response);
        }
        return $this;
    }

    /**
     * Getter for Centinel validation availability
     * @return bool
     */
    public function getIsCentinelValidationEnabled()
    {
        return false;
    }

    /**
     * Store id setter, also set storeId to helper
     * @param int $store
     */
    public function setStore($store)
    {
        $this->setData('store', $store);
        $this->_pbridgeData->setStoreId(is_object($store) ? $store->getId() : $store);
        return $this;
    }

    /**
     * Check whether 3D Secure enabled for payment gateway
     *
     * @return bool
     */
    protected function _is3DSEnabled()
    {
        return (bool)$this->getConfigData('enable3ds');
    }

    /**
     * Return true if 3D Secure checks performed on the last checkout step (Order review page)
     *
     * @return bool
     */
    public function getIsDeferred3dCheck()
    {
        return $this->_is3DSEnabled();
    }
}
