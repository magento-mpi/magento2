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
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }

    /**
     * Prepare and return Payment Bridge request url with parameters
     *
     * @param array $params
     * @return string
     */
    protected function _preparePbridgeRequestUrl($params = array())
    {
        $pbridgeUrl = trim(Mage::getStoreConfig('payment/pbridge/gatewayurl'));
        $merchantCode = trim(Mage::getStoreConfig('payment/pbridge/merchantcode'));
        $merchantKey  = trim(Mage::getStoreConfig('payment/pbridge/merchantkey'));
        $availableMethods = $this->getPbridgeUsableMethods();

        $params = array_merge(array(
            'redirect_url' => $this->_getUrl('enterprise_pbridge/pbridge/result', array('_current' => true)),
            'locale' => Mage::app()->getLocale()->getLocaleCode(),
        ), $params);
        if ($this->_getQuote()) {
            $payment = $this->_getQuote()->getPayment();
            if ($payment && $payment->getMethod()) {
                $params['quote_id']= $this->_getQuote()->getId();
                if ($payment->getMethodInstance()->getToken()) {
                    $params['token'] = $payment->getMethodInstance()->getToken();
                }
            }
        }
        $params = array_merge($params, array(
            'action'            => self::PAYMENT_GATEWAYS_CHOOSER_ACTION,
            'merchant_code'     => $merchantCode,
            'merchant_key'      => $merchantKey,
            'available_methods' => $availableMethods ? implode(',', $availableMethods) : '',
        ));

        $params = $this->encryptData($params);
        $sourceUrl = rtrim($pbridgeUrl, '/') . '/bridge.php?' . http_build_query($params);

        return $sourceUrl;
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
     * Retrieve Payment Bridge url with required parameters
     *
     * @param array $params
     * @return string
     */
    public function getPbridgeUrl($params = array())
    {
        return $this->_preparePbridgeRequestUrl($params);
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
     * @param array $data
     * @return array
     */
    public function decryptData($data)
    {
        if (!($data && is_array($data) && isset($data['data']))) {
            return array();
        }
        $data = unserialize($this->getEncryptor()->decrypt($data['data']));

        return $data;
    }

    /**
     * Encrypt data array
     *
     * @param array $data
     * @return array
     */
    public function encryptData($data)
    {
        if (!($data && is_array($data))) {
            return array();
        }
        $data = array('data' => $this->getEncryptor()->encrypt(serialize($data)));

        return $data;
    }

    /**
     * Retrieve Payment Bridge specific GET parameters
     *
     * @return array
     */
    public function getPbridgeParams()
    {
        $data = $this->decryptData($this->_getRequest()->getParams());
        $data = array(
            'original_payment_method' => isset($data['original_payment_method']) ? $data['original_payment_method'] : null,
            'token'                   => isset($data['token']) ? $data['token'] : '',
            'quote_id'                => isset($data['quote_id']) ? $data['quote_id'] : '',
        );

        return $data;
    }
}
