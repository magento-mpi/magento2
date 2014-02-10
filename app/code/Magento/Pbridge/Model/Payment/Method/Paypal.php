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
namespace Magento\Pbridge\Model\Payment\Method;

class Paypal extends \Magento\Paypal\Model\Direct
{
    /**
     * Credit card form block
     *
     * @var string
     */
    protected $_formBlock;

    /**
     * Payment Bridge Payment Method Instance
     *
     * @var \Magento\Pbridge\Model\Payment\Method\Pbridge
     */
    protected $_pbridgeMethodInstance;

    /**
     * Website Payments Pro instance type
     *
     * @var $_proType string
     */
    protected $_proType = 'Magento\Pbridge\Model\Payment\Method\Paypal\Pro';

    /**
     * Pbridge data
     *
     * @var \Magento\Pbridge\Helper\Data
     */
    protected $_pbridgeData;

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
     * @param \Magento\Pbridge\Helper\Data $pbridgeData
     * @param string $formBlock
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
        \Magento\Pbridge\Helper\Data $pbridgeData,
        $formBlock,
        array $data = array()
    ) {
        $this->_pbridgeData = $pbridgeData;
        $this->_formBlock = $formBlock;
        parent::__construct(
            $eventManager,
            $paymentData,
            $coreStoreConfig,
            $logAdapterFactory,
            $logger,
            $moduleList,
            $locale,
            $centinelService,
            $proTypeFactory,
            $storeManager,
            $urlBuilder,
            $requestHttp,
            $cartFactory,
            $data
        );
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
     * @return \Magento\Pbridge\Model\Payment\Method\Pbridge
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

    /**
     * @return string
     */
    public function getTitle()
    {
        return parent::getTitle();
    }

    /**
     * Assign data to info model instance
     *
     * @param  mixed $data
     * @return \Magento\Payment\Model\Info
     */
    public function assignData($data)
    {
        $this->getPbridgeMethodInstance()->assignData($data);
        return $this;
    }

    /**
     * Retrieve information from original payment configuration
     *
     * @param string $field
     * @param null $storeId
     * @return string|null
     */
    public function getConfigData($field, $storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->getStore();
        }
        $path = 'payment/' . $this->getOriginalCode() . '/' . $field;
        return $this->_coreStoreConfig->getConfig($path, $storeId);
    }

    /**
     * Check whether payment method can be used
     *
     * @param \Magento\Sales\Model\Quote $quote
     * @return boolean
     */
    public function isAvailable($quote = null)
    {
        return $this->getPbridgeMethodInstance()->isDummyMethodAvailable($quote)
            && $this->_pro->getConfig()->isMethodAvailable(\Magento\Paypal\Model\Config::METHOD_WPP_DIRECT);
    }

    /**
     * Retrieve block type for method form generation
     *
     * @return string
     */
    public function getFormBlockType()
    {
        return $this->_formBlock;
    }

    /**
     * Prepare info instance for save
     *
     * @return \Magento\Pbridge\Model\Payment\Method\Paypal
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
     * @return \Magento\Pbridge\Model\Payment\Method\Paypal
     */
    public function validate()
    {
        $this->getPbridgeMethodInstance()->validate();
        return $this;
    }

    /**
     * @param \Magento\Object $payment
     * @param float amount
     * @return $this
     */
    public function authorize(\Magento\Object $payment, $amount)
    {
        $payment->setCart($this->_pbridgeData->preparePaypalCart($payment->getOrder()));
        $result = new \Magento\Object($this->getPbridgeMethodInstance()->authorize($payment, $amount));
        $order = $payment->getOrder();
        $result->setEmail($order->getCustomerEmail());
        $this->_importResultToPayment($result, $payment);
        return $this;
    }

    /**
     * @param \Magento\Object $payment
     * @param float amount
     * @return $this
     */
    public function capture(\Magento\Object $payment, $amount)
    {
        if (false === $this->_pro->capture($payment, $amount)) {
            $this->authorize($payment, $amount);
        }
        return $this;
    }

    /**
     * @param \Magento\Object $payment
     * @param float amount
     * @return $this
     */
    public function refund(\Magento\Object $payment, $amount)
    {
        $this->_pro->refund($payment, $amount);
        return $this;
    }

    /**
     * @param \Magento\Object $payment
     * @param float amount
     * @return $this
     */
    public function void(\Magento\Object $payment)
    {
        $this->_pro->void($payment);
        return $this;
    }

    /**
     * Disable magento centinel validation for pbridge payment methods
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
     * @return \Magento\Pbridge\Model\Payment\Method\Paypal
     */
    public function setStore($store)
    {
        $this->setData('store', $store);
        $this->_pbridgeData->setStoreId(is_object($store) ? $store->getId() : $store);
        parent::setStore($store);
        return $this;
    }
}
