<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\PbridgePaypal\Model\Payment\Method;

use Magento\Payment\Model\Checks\PaymentMethodChecksInterface;
use Magento\Payment\Model\MethodInterface;

/**
 * Paypal dummy payment method model
 */
class Paypal implements MethodInterface, PaymentMethodChecksInterface
{
    /**
     * @var \Magento\Pbridge\Helper\Data
     */
    protected $_pbridgeData;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $_paymentData;

    /**
     * @var PaypalDirect|Payflowpro
     */
    protected $_paypalMethodInstance;

    /**
     * Payment Bridge Payment Method Instance
     *
     * @var \Magento\Pbridge\Model\Payment\Method\Pbridge
     */
    protected $_pbridgeMethodInstance;

    /**
     * @var \Magento\Paypal\Model\Config
     */
    protected $_paypalConfig;

    /**
     * @param \Magento\Pbridge\Helper\Data $pbridgeData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Payment\Model\Method\Factory $paymentFactory
     * @param \Magento\Paypal\Model\Config $paypalConfig
     * @param string $paypalClassName
     */
    public function __construct(
        \Magento\Pbridge\Helper\Data $pbridgeData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Payment\Model\Method\Factory $paymentFactory,
        \Magento\Paypal\Model\Config $paypalConfig,
        $paypalClassName
    ) {
        $this->_pbridgeData = $pbridgeData;
        $this->_scopeConfig = $scopeConfig;
        $this->_paymentData = $paymentData;
        $this->_paypalConfig = $paypalConfig;
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
        $return = call_user_func_array([$this->_paypalMethodInstance, $method], $args);
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
            $this->_pbridgeMethodInstance->setAdditionalRequestParameters(
                ['BNCODE' => $this->_paypalConfig->getBuildNotationCode()]
            );
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
        return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
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
     * @param int|string|\Magento\Store\Model\Store $store
     * @return $this
     */
    public function setStore($store)
    {
        $this->_paypalMethodInstance->setData('store', $store);
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

    /**
     * Using internal pages for input payment data
     * Can be used in admin
     *
     * @return bool
     */
    public function canUseInternal()
    {
        return $this->_paypalMethodInstance->canUseInternal();
    }

    /**
     * Can be used in regular checkout
     *
     * @return bool
     */
    public function canUseCheckout()
    {
        return $this->_paypalMethodInstance->canUseCheckout();
    }

    /**
     * To check billing country is allowed for the payment method
     *
     * @param string $country
     * @return bool
     */
    public function canUseForCountry($country)
    {
        return $this->_paypalMethodInstance->canUseForCountry($country);
    }

    /**
     * Check method for processing with base currency
     *
     * @param string $currencyCode
     * @return bool
     */
    public function canUseForCurrency($currencyCode)
    {
        return $this->_paypalMethodInstance->canUseForCurrency($currencyCode);
    }
}
