<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PbridgePaypal\Model\Payment\Method;

/**
 * Paypal dummy payment method model
 */
class Paypal implements \Magento\Payment\Model\Method
{
    /**
     * @var \Magento\Pbridge\Helper\Data
     */
    protected $_pbridgeData;

    /**
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $_paymentData;

    /**
     * @var PaypalDirect|Payflowpro|PayflowDirect
     */
    protected $_paypalMethodInstance;

    /**
     * Payment Bridge Payment Method Instance
     *
     * @var \Magento\Pbridge\Model\Payment\Method\Pbridge
     */
    protected $_pbridgeMethodInstance;

    /**
     * @param \Magento\Pbridge\Helper\Data $pbridgeData
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Payment\Model\Method\Factory $paymentFactory
     * @param string $paypalClassName
     */
    public function __construct(
        \Magento\Pbridge\Helper\Data $pbridgeData,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Payment\Model\Method\Factory $paymentFactory,
        $paypalClassName
    ) {
        $this->_pbridgeData = $pbridgeData;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_paymentData = $paymentData;
        $this->_paypalMethodInstance = $paymentFactory->create(
            $paypalClassName,
            ['pbridgeData' => $pbridgeData, 'paypal' => $this]
        );
    }


    /**
     * Call method of original instance
     *
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        $return = call_user_func_array(array($this->_paypalMethodInstance, $method), $args);
        return $return === $this->_paypalMethodInstance ? $this : $return;
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
        return 'pbridge_' . $this->_paypalMethodInstance->getCode();
    }

    /**
     * Retrieve original payment method code
     *
     * @return string
     */
    public function getOriginalCode()
    {
        return $this->_paypalMethodInstance->getCode();
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
            $storeId = $this->_paypalMethodInstance->getStore();
        }
        $path = 'payment/' . $this->getOriginalCode() . '/' . $field;
        return $this->_coreStoreConfig->getConfig($path, $storeId);
    }

    /**
     * Validate payment method information object
     *
     * @return $this
     */
    public function validate()
    {
        $this->getPbridgeMethodInstance()->validate();
        return $this;
    }

    /**
     * Disable magento centinel validation for pbridge payment methods
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
     * @param int|string|\Magento\Core\Model\Store $store
     *
     * @return $this
     */
    public function setStore($store)
    {
        $this->setData('store', $store);
        $this->_pbridgeData->setStoreId(is_object($store) ? $store->getId() : $store);
        $this->_paypalMethodInstance->setStore($store);

        return $this;
    }

    /**
     * Retrieve block type for method form generation
     *
     * @return string
     */
    public function getFormBlockType()
    {
        return $this->_paypalMethodInstance->getFormBlockType();
    }

    /**
     * Retrieve payment method title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_paypalMethodInstance->getTitle();
    }
}
