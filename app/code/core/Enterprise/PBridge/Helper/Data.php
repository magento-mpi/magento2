<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_PBridge
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Pbridge helper
 *
 * @category    Enterprise
 * @package     Enterprise_PBridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_PBridge_Helper_Data extends Enterprise_Enterprise_Helper_Core_Abstract
{
    /**
     * Payment Bridge action name to fetch Payment Bridge payment gateways
     *
     * @var string
     */
    const PAYMENT_GATEWAYS_CHOOSER_ACTION = 'GatewaysChooser';

    /**
     * Payment Bridge payment methods available for the current merchant
     *
     * $var array
     */
    protected $_pbridgeAvailableMethods = array();

    /**
     * Payment Bridge payment methods available for the current merchant
     * and usable for current conditions
     *
     * $var array
     */
    protected $_pbridgeUsableMethods = array();

    /**
     * Encryptor model
     *
     * @var Enterprise_PBridge_Model_Encryption
     */
    protected $_encryptor = null;

    /**
     * Check if Payment Bridge Magento Module is enabled in configuration
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return (bool)Mage::getStoreConfigFlag('payment/pbridge/active') &&
            (bool)Mage::getStoreConfig('payment/pbridge/gatewayurl') &&
            (bool)Mage::getStoreConfig('payment/pbridge/merchantcode') &&
            (bool)Mage::getStoreConfig('payment/pbridge/merchantkey');
    }

    /**
     * Getter
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return Mage_Sales_Model_Quote | null
     */
    protected function _getQuote($quote = null)
    {
        if ($quote && $quote instanceof Mage_Sales_Model_Quote) {
            return $quote;
        }
        return null;
    }

    /**
     * Prepare and return Payment Bridge request url with parameters if passed.
     * Encrypt parameters by default.
     *
     * @param array $params OPTIONAL
     * @param boolean $encryptParams OPTIONAL true by default
     * @return string
     */
    protected function _prepareRequestUrl($params = array(), $encryptParams = true)
    {
        $pbridgeUrl = trim(Mage::getStoreConfig('payment/pbridge/gatewayurl'));

        $sourceUrl = rtrim($pbridgeUrl, '/') . '/bridge.php';

        if (!empty($params)) {
            if ($encryptParams) {
                $params = array('data' => $this->encrypt(serialize($params)));
            }
            $sourceUrl .= '?' . http_build_query($params);
        }

        return $sourceUrl;
    }

    /**
     * Prepare required request params.
     * Optinal accept additional params to merge with required
     *
     * @param array $params OPTIONAL
     * @param Varien_Object $payment OPTIONAL
     * @return array
     */
    public function getRequestParams(array $params = array(), $quote = null)
    {
        $params = array_merge(array(
            'locale' => Mage::app()->getLocale()->getLocaleCode(),
        ), $params);

        if ($this->_getQuote($quote) && $this->_getQuote($quote)->getId()) {
            $params['quote_id'] = $this->_getQuote($quote)->getId();
            $payment = $this->_getQuote($quote)->getPayment();
            if ($payment && $payment->getMethod() && $payment->getMethodInstance()->getToken()) {
                $params['token'] = $payment->getMethodInstance()->getToken();
            }
        }

        $params['merchant_code'] = trim(Mage::getStoreConfig('payment/pbridge/merchantcode'));
        $params['merchant_key']  = trim(Mage::getStoreConfig('payment/pbridge/merchantkey'));
        return $params;
    }

    /**
     * Return payment Bridge request URL to display gateways chooser
     *
     * @param array $params OPTIONAL
     * @param Mage_Sale_Model_Quote $quote
     * @return string
     */
    public function getGatewaysChooserUrl(array $params = array(), $quote = null)
    {
        $availableMethods = $this->getPbridgeUsableMethods();
        if ($availableMethods) {
            $params = array_merge(array(
                'available_methods' => implode(',', $availableMethods)
            ), $params);
        }

        $quote = $this->_getQuote($quote);
        $params = array_merge(array(
            'order_id'      => $quote ? $quote->getReservedOrderId() : '',
            'amount'        => $quote ? $quote->getBaseGrandTotal() : '0',
            'currency_code' => $quote ? $quote->getBaseCurrencyCode() : ''
        ), $params);

        $params = $this->getRequestParams($params, $quote);
        $params['action'] = self::PAYMENT_GATEWAYS_CHOOSER_ACTION;
        return $this->_prepareRequestUrl($params, true);
    }

    /**
     * Getter.
     * Retrieve Payment Bridge url
     *
     * @param array $params
     * @return string
     */
    public function getRequestUrl()
    {
        return $this->_prepareRequestUrl();
    }

    /**
     * Prepare given payment method and return Payment Bridge payment methods
     * available for the current merchant
     *
     * @param string $method
     * @return array
     */
    protected function _preparePbridgeAvailableMethod($method)
    {
        if (!in_array($method, $this->_pbridgeAvailableMethods)) {
            if (Mage::getStoreConfigFlag('payment/' . $method . '/using_pbridge')) {
                $this->_pbridgeAvailableMethods[] = $method;
            }
        }
        return $this->_pbridgeAvailableMethods;
    }

    /**
     * Getter.
     * Retrieve Payment Bridge payment methods available for the current merchant
     *
     * @return array
     */
    public function getPbridgeAvailableMethods()
    {
        return $this->_pbridgeAvailableMethods;
    }

    /**
     * Check if the payment method is within the list of available for current merchant
     *
     * @param string $method
     * @return bool
     */
    public function isAvailablePbridgeMethod($method)
    {
        return in_array($method, $this->_preparePbridgeAvailableMethod($method));
    }

    /**
     * Setter.
     * Set specified method into the array of usable methods
     *
     * @param string $method
     * @return Enterprise_PBridge_Helper_Data
     */
    public function setPbridgeMethodUsable($method)
    {
        if (!isset($this->_pbridgeUsableMethods[$method])) {
            $this->_pbridgeUsableMethods[$method] = true;
        }
        return $this;
    }

    /**
     * Setter.
     * Remove specified method from the array of usable methods
     *
     * @param string $method
     */
    public function unsetPbridgeMethodUsable($method)
    {
        $this->_pbridgeUsableMethods[$method] = false;
        return $this;
    }

    /**
     * Check if the payment method is within the list of usable under current conditions
     *
     * @param string $method
     * @return bool
     */
    public function isPbridgeMethodUsable($method)
    {
        return isset($this->_pbridgeUsableMethods[$method]) && $this->_pbridgeUsableMethods[$method] === true;
    }

    /**
     * Getter.
     * Retrieve Payment Bridge payment methods usable under current conditions
     *
     * @return array
     */
    public function getPbridgeUsableMethods()
    {
        $result = array();
        foreach ($this->_pbridgeAvailableMethods as $method) {
            if ($this->isPbridgeMethodUsable($method)) {
                $result[] = $method;
            }
        }
        return $result;
    }

    /**
     * Return a modified encryptor
     *
     * @return Enterprise_PBridge_Model_Encryption
     */
    public function getEncryptor()
    {
        if ($this->_encryptor === null) {
            $this->_encryptor = Mage::getModel('enterprise_pbridge/encryption');
            $this->_encryptor->setHelper($this);
        }
        return $this->_encryptor;
    }

    /**
     * Decrypt data array
     *
     * @param string $data
     * @return string
     */
    public function decrypt($data)
    {
        return $this->getEncryptor()->decrypt($data);
    }

    /**
     * Encrypt data array
     *
     * @param string $data
     * @return string
     */
    public function encrypt($data)
    {
        return $this->getEncryptor()->encrypt($data);
    }

    /**
     * Retrieve Payment Bridge specific GET parameters
     *
     * @return array
     */
    public function getPbridgeParams()
    {
        $data = unserialize($this->decrypt($this->_getRequest()->getParam('data', '')));
        $data = array(
            'original_payment_method' => isset($data['original_payment_method']) ? $data['original_payment_method'] : null,
            'token'                   => isset($data['token']) ? $data['token'] : '',
            'quote_id'                => isset($data['quote_id']) ? $data['quote_id'] : '',
        );

        return $data;
    }

    /**
     * Prepare cart from order
     *
     * @param Mage_Core_Model_Abstract $order
     * @return array
     */
    public function prepareCart($order)
    {
        return Mage::helper('paypal')->prepareLineItems($order);
    }
}
