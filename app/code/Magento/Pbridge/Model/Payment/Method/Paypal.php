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
 * Paypal Direct dummy payment method model
 */
class Magento_Pbridge_Model_Payment_Method_Paypal extends Magento_Paypal_Model_Direct
{
    /**
     * Form block type for the frontend
     *
     * @var string
     */
    protected $_formBlockType = 'Magento_Pbridge_Block_Checkout_Payment_Paypal';

    /**
     * Form block type for the backend
     *
     * @var string
     */
    protected $_backendFormBlockType = 'Magento_Pbridge_Block_Adminhtml_Sales_Order_Create_Paypal';

    /**
     * Payment Bridge Payment Method Instance
     *
     * @var Magento_Pbridge_Model_Payment_Method_Pbridge
     */
    protected $_pbridgeMethodInstance = null;

    /**
     * Website Payments Pro instance type
     *
     * @var $_proType string
     */
    protected $_proType = 'Magento_Pbridge_Model_Payment_Method_Paypal_Pro';

    /**
     * Pbridge data
     *
     * @var Magento_Pbridge_Helper_Data
     */
    protected $_pbridgeData = null;

    /**
     * Construct
     *
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Pbridge_Helper_Data $pbridgeData
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_ModuleListInterface $moduleList
     * @param Magento_Payment_Helper_Data $paymentData
     * @param Magento_Core_Model_Log_AdapterFactory $logAdapterFactory
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Centinel_Model_Service $service
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Pbridge_Helper_Data $pbridgeData,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_ModuleListInterface $moduleList,
        Magento_Payment_Helper_Data $paymentData,
        Magento_Core_Model_Log_AdapterFactory $logAdapterFactory,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Centinel_Model_Service $service,
        array $data = array()
    ) {
        $this->_pbridgeData = $pbridgeData;
        parent::__construct($eventManager, $coreStoreConfig, $moduleList, $paymentData, $logAdapterFactory,
            $locale, $service, $data);
        $this->_pro->setPaymentMethod($this);
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
            $this->_pbridgeMethodInstance->setOriginalMethodInstance($this);
        }
        return $this->_pbridgeMethodInstance;
    }

    /**
     * Retrieve dummy payment method code
     *
     * @return string
     */
    public function getCode()
    {
        return 'pbridge_' . parent::getCode();
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
     * Retrieve information from original payment configuration
     *
     * @param   string $field
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
        return $this->getPbridgeMethodInstance()->isDummyMethodAvailable($quote)
            && $this->_pro->getConfig()->isMethodAvailable(Magento_Paypal_Model_Config::METHOD_WPP_DIRECT);
    }

    /**
     * Retrieve block type for method form generation
     *
     * @return string
     */
    public function getFormBlockType()
    {
        return Mage::app()->getStore()->isAdmin() ?
            $this->_backendFormBlockType :
            $this->_formBlockType;
    }

    /**
     * Prepare info instance for save
     *
     * @return Magento_Pbridge_Model_Payment_Method_Paypal
     */
    public function prepareSave()
    {
//        $info = $this->getInfoInstance();
//        if ($this->_canSaveCc) {
//            $info->setCcNumberEnc($info->encrypt($info->getCcNumber()));
//        }
//        //$info->setCcCidEnc($info->encrypt($info->getCcCid()));
//        $info->setCcNumber(null)
//            ->setCcCid(null);
        return $this;
    }

    /**
     * Validate payment method information object
     *
     * @return Magento_Pbridge_Model_Payment_Method_Paypal
     */
    public function validate()
    {
        $this->getPbridgeMethodInstance()->validate();
        return $this;
    }

    public function authorize(Magento_Object $payment, $amount)
    {
        $result = new Magento_Object($this->getPbridgeMethodInstance()->authorize($payment, $amount));
        $order = $payment->getOrder();
        $result->setEmail($order->getCustomerEmail());
        $this->_importResultToPayment($result, $payment);
        return $this;
    }

    public function capture(Magento_Object $payment, $amount)
    {
        if (false === $this->_pro->capture($payment, $amount)) {
            $this->authorize($payment, $amount);
        }
        return $this;
    }

    public function refund(Magento_Object $payment, $amount)
    {
        $this->_pro->refund($payment, $amount);
        return $this;
    }

    public function void(Magento_Object $payment)
    {
        $this->_pro->void($payment);
        return $this;
    }

    /**
     * Disable magento centinel validation for pbridge payment methods
     */
    public function getIsCentinelValidationEnabled()
    {
        return false;
    }

    /**
     * Store id setter, also set storeId to helper
     *
     * @param int $store
     */
    public function setStore($store)
    {
        $this->setData('store', $store);
        $this->_pbridgeData->setStoreId(is_object($store) ? $store->getId() : $store);
        parent::setStore($store);
        return $this;
    }
}
