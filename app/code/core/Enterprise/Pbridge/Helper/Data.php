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
 * @package     Enterprise_Pbridge
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Pbridge helper
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Pbridge_Helper_Data extends Enterprise_Enterprise_Helper_Core_Abstract
{
    /**
     * Payment Bridge action name to fetch Payment Bridge gateway form
     *
     * @var string
     */
    const PAYMENT_GATEWAY_FORM_ACTION = 'GatewayForm';

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
     * @var Enterprise_Pbridge_Model_Encryption
     */
    protected $_encryptor = null;

    /**
     * Check if Payment Bridge Magento Module is enabled in configuration
     *
     * @return boolean
     */
    public function isEnabled($store = null)
    {
        return (bool)Mage::getStoreConfigFlag('payment/pbridge/active', $store) && $this->isAvailable($store);
    }

    /**
     * Check if enough config paramters to use Pbridge module
     *
     * @param Mage_Core_Model_Store | integer $store
     * @return boolean
     */
    public function isAvailable($store = null)
    {
        return (bool)Mage::getStoreConfig('payment/pbridge/gatewayurl', $store) &&
            (bool)Mage::getStoreConfig('payment/pbridge/merchantcode', $store) &&
            (bool)Mage::getStoreConfig('payment/pbridge/merchantkey', $store);
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
        }

        $params['merchant_code'] = trim(Mage::getStoreConfig('payment/pbridge/merchantcode'));

        $sourceUrl .= '?' . http_build_query($params);

        return $sourceUrl;
    }

    /**
     * Prepare required request params.
     * Optinal accept additional params to merge with required
     *
     * @param array $params OPTIONAL
     * @return array
     */
    public function getRequestParams(array $params = array())
    {
        $params = array_merge(array(
            'locale' => Mage::app()->getLocale()->getLocaleCode(),
        ), $params);

        $params['merchant_key']  = trim(Mage::getStoreConfig('payment/pbridge/merchantkey'));

        return $params;
    }

    /**
     * Return payment Bridge request URL to display gateway form
     *
     * @param array $params OPTIONAL
     * @param Mage_Sale_Model_Quote $quote
     * @return string
     */
    public function getGatewayFormUrl(array $params = array(), $quote = null)
    {
        $quote = $this->_getQuote($quote);
        $params = array_merge(array(
            'order_id'      => $quote ? $quote->getReservedOrderId() : '',
            'amount'        => $quote ? $quote->getBaseGrandTotal() : '0',
            'currency_code' => $quote ? $quote->getBaseCurrencyCode() : ''
        ), $params);

        $params = $this->getRequestParams($params, $quote);
        $params['action'] = self::PAYMENT_GATEWAY_FORM_ACTION;
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
     * Return a modified encryptor
     *
     * @return Enterprise_Pbridge_Model_Encryption
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
