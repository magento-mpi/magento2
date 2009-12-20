<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract class for Paypal API wrappers
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Paypal_Model_Api_Abstract extends Varien_Object
{
    /**
     * Global private to public interface map
     * @var array
     */
    protected $_globalMap = array();

    /**
     * Filter callbacks for importing/exporting amount
     * @var array
     */
    protected $_exportToRequestFilters = array();

    const FRAUD_ERROR_CODE = 11610;

    const AVS_RESPONSE_MATCH          = 'Y';
    const AVS_RESPONSE_NO_MATCH       = 'N';
    const AVS_RESPONSE_NO_CARDHOLDER  = 'X';
    const AVS_RESPONSE_ALL            = 0;
    const AVS_RESPONSE_NONE           = 1;
    const AVS_RESPONSE_PARTIAL        = 2;
    const AVS_RESPONSE_NOT_PROCESSED  = 3;
    const AVS_RESPONSE_NOT_AVAILIABLE = 4;

    const CVV_RESPONSE_MATCH_CC                 = 'M';
    const CVV_RESPONSE_MATCH_SOLO               = 0;
    const CVV_RESPONSE_NOT_MATCH_CC             = 'N';
    const CVV_RESPONSE_NOT_MATCH_SOLO           = 1;
    const CVV_RESPONSE_NOT_PROCESSED_CC         = 'P';
    const CVV_RESPONSE_NOT_IMPLEMENTED_SOLO     = 2;
    const CVV_RESPONSE_NOT_SUPPORTED_CC         = 'S';
    const CVV_RESPONSE_NOT_PRESENT_SOLO         = 3;
    const CVV_RESPONSE_NOT_AVAILIBLE_CC         = 'U';
    const CVV_RESPONSE_NOT_AVAILIBLE_SOLO       = 4;
    const CVV_RESPONSE_NOT_RESPONSE_CC          = 'X';

    /**
     * return server name from as server variable
     *
     * @return string
     */
    public function getServerName()
    {
        if (!$this->hasServerName()) {
            $this->setServerName($_SERVER['SERVER_NAME']);
        }
        return $this->getData('server_name');
    }

    /**
     * Return config data based on paymethod, store id
     *
     * @return string
     */
    public function getConfigData($key, $default=false, $storeId = null)
    {
        return $this->_getGeneralConfigData($key, $default, $storeId, 'paypal/wpp/');
    }

    /**
     * Get PayPal Account Style Configuration
     *
     */
    public function getStyleConfigData($key, $default=false, $storeId = null)
    {
        return $this->_getGeneralConfigData($key, $default, $storeId, 'paypal/style/');
    }

    /**
     * Return config data by give path, key, default and store Id
     * TODO: remove this
     *
     */
    private function _getGeneralConfigData($key, $default=false, $storeId = null, $path = 'paypal/wpp/')
    {
        if (!$this->hasData($key)) {
            if ($storeId === null && $this->getPayment() instanceof Varien_Object) {
                $storeId = $this->getPayment()->getOrder()->getStoreId();
            }
            $value = Mage::getStoreConfig($path . $key, $storeId);
            if (empty($value)) {
                $value = $default;
            }
            $this->setData($key, $value);
        }
        return $this->getData($key);
    }

    /**
     * Return paypal session model
     *
     * @return Mage_Paypal_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('paypal/session');
    }

    /**
     * Flag which check if we are use session or not.
     *
     * @return bool
     */
    public function getUseSession()
    {
        if (!$this->hasData('use_session')) {
            $this->setUseSession(true);
        }
        return $this->getData('use_session');
    }

    /**
     * Return data from session based on key and default value
     *
     * @param $key string
     * @param $default string
     *
     * @return string
     */
    public function getSessionData($key, $default=false)
    {
        if (!$this->hasData($key)) {
            $value = $this->getSession()->getData($key);
            if ($this->getSession()->hasData($key)) {
                $value = $this->getSession()->getData($key);
            } else {
                $value = $default;
            }
            $this->setData($key, $value);
        }
        return $this->getData($key);
    }

    /**
     * Set data in session scope
     *
     * @param $key string
     * @param $value string
     *
     * @return Mage_Paypal_Model_Api_Abstract
     */
    public function setSessionData($key, $value)
    {
        if ($this->getUseSession()) {
            $this->getSession()->setData($key, $value);
        }
        $this->setData($key, $value);
        return $this;
    }

    /**
     * Return sandbox flag state, by config
     *
     * @return bool
     */
    public function getSandboxFlag()
    {
        return $this->getConfigData('sandbox_flag', true);
    }

    /**
     * Return Paypal Api user name based on config data
     *
     * @return string
     */
    public function getApiUsername()
    {
        return $this->getConfigData('api_username');
    }

    /**
     * Return Paypal Api password based on config data
     *
     * @return string
     */
    public function getApiPassword()
    {
        return $this->getConfigData('api_password');
    }

    /**
     * Return Paypal Api signature based on config data
     *
     * @return string
     */
    public function getApiSignature()
    {
        return $this->getConfigData('api_signature');
    }

    /**
     * Return Paypal Express check out button source
     *
     * @return string
     */
    public function getButtonSourceEc()
    {
        return $this->getConfigData('button_source_ec', 'Varien_Cart_EC_US');
    }

    /**
     * Return Paypal direct payment button source
     *
     * @return string
     */
    public function getButtonSourceDp()
    {
        return $this->getConfigData('button_source_dp', 'Varien_Cart_DP_US');
    }

    /**
     * Return Paypal Api proxy status based on config data
     *
     * @return bool
     */
    public function getUseProxy()
    {
        return $this->getConfigData('use_proxy', false);
    }

    /**
     * Return Paypal Api proxy host based on config data
     *
     * @return string
     */
    public function getProxyHost()
    {
        return $this->getConfigData('proxy_host', '127.0.0.1');
    }

    /**
     * Return Paypal Api proxy port based on config data
     *
     * @return string
     */
    public function getProxyPort()
    {
        return $this->getConfigData('proxy_port', '808');
    }

    /**
     * Return Paypal Api debug flag based on config data
     *
     * @return bool
     */
    public function getDebug()
    {
        return $this->getConfigData('debug_flag', true);
    }

    /**
     * Get authorization id from session data
     *
     * @return string
     */
    public function getAuthorizationId()
    {
        return $this->getSessionData('authorization_id');
    }

    /**
     * Set authorization id in session
     *
     * @param $data string
     *
     * @return Mage_Paypal_Model_Api_Abstract
     *
     */
    public function setAuthorizationId($data)
    {
        return $this->setSessionData('authorization_id', $data);
    }

    /**
     * Complete type code (Complete, NotComplete)
     *
     * @return string
     */
    public function getCompleteType()
    {
        return $this->getSessionData('complete_type');
    }

    /**
     * Set Complite type code in session
     *
     * @param $data string
     *
     * @return Mage_Paypal_Model_Api_Abstract
     *
     */
    public function setCompleteType($data)
    {
        return $this->setSessionData('complete_type', $data);
    }

    /**
     * Has to be one of the following values: Sale or Order or Authorization
     *
     * @return string
     */
    public function getPaymentType()
    {
        return $this->getSessionData('payment_type');
    }

    /**
     * Set payment type in session as paypal response comes result
     *
     * @param $data string
     *
     * @return Mage_Paypal_Model_Api_Abstract
     */
    public function setPaymentType($data)
    {
        return $this->setSessionData('payment_type', $data);
    }

    /**
     * Total value of the shopping cart
     *
     * Includes taxes, shipping costs, discount, etc.
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->getSessionData('amount');
    }

    /**
     * Set payment amount in session
     *
     * @param $data string
     *
     * @return Mage_Paypal_Model_Api_Abstract
     */
    public function setAmount($amount)
    {
        $amount = sprintf('%.2F', $amount);
        return $this->setSessionData('amount', $amount);
    }

    /**
     * Set currency code in session
     *
     * @param $data string
     *
     * @return Mage_Paypal_Model_Api_Abstract
     */
    public function setCurrencyCode($data)
    {
        return $this->setSessionData('currency_code', $data);
    }

    /**
     * Refund type ('Full', 'Partial')
     *
     * @return string
     */
    public function getRefundType()
    {
        return $this->getSessionData('refund_type');
    }

    /**
     * Set payment return type in session as a result of paypal response come
     *
     * @param $data string
     *
     * @return Mage_Paypal_Model_Api_Abstract
     */
    public function setRefundType($data)
    {
        return $this->setSessionData('refund_type', $data);
    }

    /**
     * Return paypal request errors, get error message from response and set in session
     *
     * @return string
     */
    public function getError()
    {
        return $this->getSessionData('error');
    }


    /**
     * Error message getter intended to be based on error session data
     * @return string
     */
    public function getErrorMessage()
    {
        return '';
    }

    /**
     * Set paypal request error data in session
     *
     * @param $data string
     *
     * @return Mage_Paypal_Model_Api_Abstract
     */
    public function setError($data)
    {
        return $this->setSessionData('error', $data);
    }

    /**
     * Return ccType title by given type code
     *
     * @return string
     */
    public function getCcTypeName($ccType)
    {
        $types = array('AE'=>Mage::helper('paypal')->__('Amex'), 'VI'=>Mage::helper('paypal')->__('Visa'), 'MC'=>Mage::helper('paypal')->__('MasterCard'), 'DI'=>Mage::helper('paypal')->__('Discover'));
        return isset($types[$ccType]) ? $types[$ccType] : false;
    }

    /**
     * Reset session error scope
     *
     * @return Mage_Paypal_Model_Api_Abstract
     */
    public function unsError()
    {
        return $this->setSessionData('error', null);
    }

    /**
     * Import $this public data to specified object or array
     *
     * @param array|Varien_Object $to
     * @param array $publicMap
     * @return array|Varien_Object
     */
    public function &import($to, array $publicMap = array())
    {
        return Varien_Object_Mapper::accumulateByMap(array($this, 'getDataUsingMethod'), $to, $publicMap);
    }

    /**
     * Export $this public data from specified object or array
     *
     * @param array|Varien_Object $from
     * @param array $publicMap
     * @return Mage_Paypal_Model_Api_Abstract
     */
    public function export($from, array $publicMap = array())
    {
        Varien_Object_Mapper::accumulateByMap($from, array($this, 'setDataUsingMethod'), $publicMap);
        return $this;
    }

    /**
     * Export $this public data to private request array
     *
     * @param array $internalRequestMap
     * @param array $request
     * @return array
     */
    protected function &_exportToRequest(array $privateRequestMap, array $request = array())
    {
        $map = array();
        foreach ($privateRequestMap as $key) {
            $map[$this->_globalMap[$key]] = $key;
        }
        $result = Varien_Object_Mapper::accumulateByMap(array($this, 'getDataUsingMethod'), $request, $map);
        foreach ($privateRequestMap as $key) {
            if (isset($this->_exportToRequestFilters[$key])) {
                $result[$key] = call_user_func(array($this, $this->_exportToRequestFilters[$key]),
                    $result[$key], $map[$this->_globalMap[$key]]
                );
            }
        }
        return $result;
    }

    /**
     * Import $this public data from a private response array
     *
     * @param array $privateResponseMap
     * @param array $response
     */
    protected function _importFromResponse(array $privateResponseMap, array $response)
    {
        $map = array();
        foreach ($privateResponseMap as $key) {
            $map[$key] = $this->_globalMap[$key];
        }
        Varien_Object_Mapper::accumulateByMap($response, array($this, 'setDataUsingMethod'), $map);
    }

    /**
     * Filter amounts in API calls
     * @param float|string $value
     * @return string
     */
    protected function _filterAmount($value)
    {
        return sprintf('%.2F', $value);
    }

    /**
     * Get AVS proper text by given AVS response code
     *
     * @return string
     */
    public function getAvsDetail($avsCode)
    {
        switch ($avsCode) {
                    case self::AVS_RESPONSE_MATCH:
                return Mage::helper('paypal')->__('All the address information matched.');
            case self::AVS_RESPONSE_NONE:
            case self::AVS_RESPONSE_NO_MATCH:
                return Mage::helper('paypal')->__('None of the address information matched.');
            case self::AVS_RESPONSE_PARTIAL :
                return Mage::helper('paypal')->__('Part of the address information matched.');
            case self::AVS_RESPONSE_NOT_AVAILIABLE :
                return Mage::helper('paypal')->__('Address not checked, or acquirer had no response. Service not available.');
            case self::AVS_RESPONSE_NO_CARDHOLDER:
                return Mage::helper('paypal')->__('Cardholder\'s bank doesn\'t support address verification');
            case self::AVS_RESPONSE_NOT_PROCESSED :
                return Mage::helper('paypal')->__('The merchant did not provide AVS information. Not processed.');
            default:
                if ($avsCode === self::AVS_RESPONSE_ALL) {
                    return Mage::helper('paypal')->__('All the address information matched.');
                } else {
                    return '';
                }
        }
    }

    /**
     * Return mapped CVV text by given cvv code
     *
     * @return string
     */
    public function getCvvDetail($cvvCode)
    {
        switch ($cvvCode) {
        case self::CVV_RESPONSE_MATCH_CC:
            return Mage::helper('paypal')->__('Matched');
        case self::CVV_RESPONSE_NOT_MATCH_CC:
        case self::CVV_RESPONSE_NOT_MATCH_SOLO:
            return Mage::helper('paypal')->__('No match');
        case self::CVV_RESPONSE_NOT_PROCESSED_CC :
            return Mage::helper('paypal')->__('Not processed');
        case self::CVV_RESPONSE_NOT_IMPLEMENTED_SOLO :
            return Mage::helper('paypal')->__('The merchant has not implemented CVV2 code handling');
        case self::CVV_RESPONSE_NOT_SUPPORTED_CC :
            return Mage::helper('paypal')->__('Service not supported');
        case self::CVV_RESPONSE_NOT_PRESENT_SOLO :
            return Mage::helper('paypal')->__('Merchant has indicated that CVV2 is not present on card');
        case self::CVV_RESPONSE_NOT_AVAILIBLE_CC :
        case self::CVV_RESPONSE_NOT_AVAILIBLE_SOLO :
            return Mage::helper('paypal')->__('Service not available');
        case self::CVV_RESPONSE_NOT_RESPONSE_CC :
            return Mage::helper('paypal')->__('No response');
        default:
            if (self::CVV_RESPONSE_MATCH_SOLO === $cvvCode) {
                return Mage::helper('paypal')->__('Matched');
            } else {
                return '';
            }
        }
    }
}
