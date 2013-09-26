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
     * Form block type for the frontend
     *
     * @var string
     */
    protected $_formBlockType = 'Magento\Pbridge\Block\Checkout\Payment\Payflow\Pro';

    /**
     * Form block type for the backend
     *
     * @var string
     */
    protected $_backendFormBlockType = 'Magento\Pbridge\Block\Adminhtml\Sales\Order\Create\Payflow\Pro';

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
    protected $_pbridgeData = null;

    /**
     * Construct
     *
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Pbridge\Helper\Data $pbridgeData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Model\ModuleListInterface $moduleList
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Core\Model\Log\AdapterFactory $logAdapterFactory
     * @param \Magento\Core\Model\LocaleInterface $locale,
     * @param \Magento\Centinel\Model\Service $centinelService,
     * @param \Magento\Core\Model\Logger $logger
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Logger $logger,
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Pbridge\Helper\Data $pbridgeData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Model\ModuleListInterface $moduleList,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Core\Model\Log\AdapterFactory $logAdapterFactory,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Centinel\Model\Service $centinelService,
        array $data = array()
    ) {
        $this->_pbridgeData = $pbridgeData;
        parent::__construct($logger, $eventManager, $coreStoreConfig, $coreData, $moduleList, $paymentData,
            $logAdapterFactory, $locale, $centinelService, $data);
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
     * @param \Magento\Sales\Model\Quote $quote
     * @return boolean
     */
    public function isAvailable($quote = null)
    {
        $storeId = \Mage::app()->getStore($this->getStore())->getId();
        $config = \Mage::getModel('Magento\Paypal\Model\Config')->setStoreId($storeId);

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
        return \Mage::app()->getStore()->isAdmin() ?
            $this->_backendFormBlockType :
            $this->_formBlockType;
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
     */
    public function setStore($store)
    {
        $this->setData('store', $store);
        $this->_pbridgeData->setStoreId(is_object($store) ? $store->getId() : $store);
        return $this;
    }
}
