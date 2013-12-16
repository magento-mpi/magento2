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
 * Authoreze.Net dummy payment method model
 */
namespace Magento\Pbridge\Model\Payment\Method\Payflow;

class Pro extends \Magento\Paypal\Model\Payflowpro
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
    protected $_pbridgeMethodInstance = null;
    protected $_canFetchTransactionInfo = false;

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
     * @param \Magento\Core\Model\Log\AdapterFactory $logAdapterFactory
     * @param \Magento\Logger $logger
     * @param \Magento\Module\ModuleListInterface $moduleList
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Centinel\Model\Service $centinelService
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Paypal\Model\ConfigFactory $configFactory
     * @param \Magento\Math\Random $mathRandom
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
        \Magento\Core\Model\Log\AdapterFactory $logAdapterFactory,
        \Magento\Logger $logger,
        \Magento\Module\ModuleListInterface $moduleList,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Centinel\Model\Service $centinelService,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Paypal\Model\ConfigFactory $configFactory,
        \Magento\Math\Random $mathRandom,
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
            $storeManager,
            $configFactory,
            $mathRandom,
            $data
        );
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
     * Retrieve information from payment configuration
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
        $storeId = $this->_storeManager->getStore($this->getStore())->getId();
        $config = $this->_configFactory->create()->setStoreId($storeId);

        return $this->getPbridgeMethodInstance()->isDummyMethodAvailable($quote)
            && $config->isMethodAvailable($this->getOriginalCode());
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
     * Validate payment method information object
     *
     * @return \Magento\Pbridge\Model\Payment\Method\Payflow\Pro
     */
    public function validate()
    {
        $this->getPbridgeMethodInstance()->validate();
        return $this;
    }

    /**
     * Authorization method being executed via Payment Bridge
     *
     * @param \Magento\Object $payment
     * @param float $amount
     * @return \Magento\Pbridge\Model\Payment\Method\Payflow\Pro
     */
    public function authorize(\Magento\Object $payment, $amount)
    {
        $response = $this->getPbridgeMethodInstance()->authorize($payment, $amount);
        $payment->addData((array)$response);
        $payment->setIsTransactionClosed(0);
        return $this;
    }

    /**
     * Capturing method being executed via Payment Bridge
     *
     * @param \Magento\Object $payment
     * @param float $amount
     * @return \Magento\Pbridge\Model\Payment\Method\Payflow\Pro
     */
    public function capture(\Magento\Object $payment, $amount)
    {
        $payment->setShouldCloseParentTransaction(!$this->_getCaptureAmount($amount));
        $payment->setFirstCaptureFlag(!$this->getInfoInstance()->hasAmountPaid());
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
     * @param \Magento\Object $payment
     * @param float $amount
     * @return \Magento\Pbridge\Model\Payment\Method\Payflow\Pro
     */
    public function refund(\Magento\Object $payment, $amount)
    {
        $response = $this->getPbridgeMethodInstance()->refund($payment, $amount);
        $payment->addData((array)$response);
        $payment->setShouldCloseParentTransaction(!$payment->getCreditmemo()->getInvoice()->canRefund());
        return $this;
    }

    /**
     * Voiding method being executed via Payment Bridge
     *
     * @param \Magento\Object $payment
     * @return \Magento\Pbridge\Model\Payment\Method\Payflow\Pro
     */
    public function void(\Magento\Object $payment)
    {
        $response = $this->getPbridgeMethodInstance()->void($payment);
        $payment->addData((array)$response);
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
     * @return $this
     */
    public function setStore($store)
    {
        $this->setData('store', $store);
        $this->_pbridgeData->setStoreId(is_object($store) ? $store->getId() : $store);
        return $this;
    }
}
