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
     * Config instance
     * @var Mage_Paypal_Model_Config
     */
    protected $_config = null;

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

    /**
     * Line items export to request mapping settings
     * @var array
     */
    protected $_lineItemExportTotals = array();
    protected $_lineItemExportItemsFormat = array();

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
     * Return Paypal Api user name based on config data
     *
     * @return string
     */
    public function getApiUsername()
    {
        return $this->_config->apiUsername;
    }

    /**
     * Return Paypal Api password based on config data
     *
     * @return string
     */
    public function getApiPassword()
    {
        return $this->_config->apiPassword;
    }

    /**
     * Return Paypal Api signature based on config data
     *
     * @return string
     */
    public function getApiSignature()
    {
        return $this->_config->apiSignature;
    }

    /**
     * BN code getter
     *
     * @return string
     */
    public function getBuildNotationCode()
    {
        return $this->_config->getBuildNotationCode();
    }

    /**
     * Return Paypal Api proxy status based on config data
     *
     * @return bool
     */
    public function getUseProxy()
    {
        return $this->_getDataOrConfig('use_proxy', false);
    }

    /**
     * Return Paypal Api proxy host based on config data
     *
     * @return string
     */
    public function getProxyHost()
    {
        return $this->_getDataOrConfig('proxy_host', '127.0.0.1');
    }

    /**
     * Return Paypal Api proxy port based on config data
     *
     * @return string
     */
    public function getProxyPort()
    {
        return $this->_getDataOrConfig('proxy_port', '808');
    }

    /**
     * Return Paypal Api debug flag based on config data
     *
     * @return bool
     */
    public function getDebug()
    {
        return $this->_config->debugFlag;
    }

    /**
     * PayPal page CSS getter
     *
     * @return string
     */
    public function getPageStyle()
    {
        return $this->_getDataOrConfig('page_style');
    }

    /**
     * Logo URL getter
     *
     * @return string
     */
    public function getLogoUrl()
    {
        return $this->_getDataOrConfig('logo_url');
    }

    /**
     * PayPal page header image URL getter
     *
     * @return string
     */
    public function getHdrimg()
    {
        return $this->_getDataOrConfig('paypal_hdrimg');
    }

    /**
     * PayPal page header border color getter
     *
     * @return string
     */
    public function getHdrbordercolor()
    {
        return $this->_getDataOrConfig('paypal_hdrbordercolor');
    }

    /**
     * PayPal page header background color getter
     *
     * @return string
     */
    public function getHdrbackcolor()
    {
        return $this->_getDataOrConfig('paypal_hdrbackcolor');
    }

    /**
     * PayPal page "payflow color" (?) getter
     *
     * @return string
     */
    public function getPayflowcolor()
    {
        return $this->_getDataOrConfig('paypal_payflowcolor');
    }

    /**
     * Payment action getter
     *
     * @return string
     */
    public function getPaymentAction()
    {
        return $this->_getDataOrConfig('payment_action');
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
     * Config instance setter
     * @param Mage_Paypal_Model_Config $config
     * @return Mage_Paypal_Model_Api_Abstract
     */
    public function setConfigObject(Mage_Paypal_Model_Config $config)
    {
        $this->_config = $config;
        return $this;
    }

    /**
     * Current locale code getter
     *
     * @return string
     */
    public function getLocaleCode()
    {
        return Mage::app()->getLocale()->getLocaleCode();
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
            if (isset($this->_exportToRequestFilters[$key]) && isset($result[$key])) {
                $callback   = $this->_exportToRequestFilters[$key];
                $privateKey = $result[$key];
                $publicKey  = $map[$this->_globalMap[$key]];
                $result[$key] = call_user_func(array($this, $callback), $privateKey, $publicKey);
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
     * Prepare line items request
     *
     * @param array &$request
     * @param int $i
     */
    protected function _exportLineItems(array &$request, $i = 0)
    {
        $items = $this->getLineItems();
        if (empty($items)) {
            return;
        }
        // line items
        foreach ($items as $item) {
            foreach ($this->_lineItemExportItemsFormat as $publicKey => $privateFormat) {
                $value = $item->getDataUsingMethod($publicKey);
                if (is_float($value)) {
                    $value = $this->_filterAmount($value);
                }
                $request[sprintf($privateFormat, $i)] = $value;
            }
            $i++;
        }
        // line item totals
        $lineItemTotals = $this->getLineItemTotals();
        if ($lineItemTotals) {
            $request = Varien_Object_Mapper::accumulateByMap($lineItemTotals, $request, $this->_lineItemExportTotals);
            foreach ($this->_lineItemExportTotals as $privateKey) {
                if (isset($request[$privateKey])) {
                    $request[$privateKey] = $this->_filterAmount($request[$privateKey]);
                } else {
                    Mage::logException(new Exception(sprintf('Missing index "%s" for line item totals.', $privateKey)));
                    Mage::throwException(Mage::helper('paypal')->__('Unable to calculate cart line item totals.'));
                }
            }
        }
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
     * Unified getter that looks in data or falls back to config
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function _getDataOrConfig($key, $default = null)
    {
        if ($this->hasData($key)) {
            return $this->getData($key);
        }
        return $this->_config->$key ? $this->_config->$key : $default;
    }


    /**
     * region_id workaround: PayPal requires state code, try to find one in the address
     *
     * @param Varien_Object $address
     * @return string
     */
    protected function _lookupRegionCodeFromAddress(Varien_Object $address)
    {
        if ($regionId = $address->getData('region_id')) {
            $region = Mage::getModel('directory/region')->load($regionId);
            if ($region->getId()) {
                return $region->getCode();
            }
        }
        return '';
    }

    /**
     * Street address workaround: divides address lines into parts by specified keys
     * (keys should go as 3rd, 4th[...] parameters)
     *
     * @param Varien_Object $address
     * @param array $request
     */
    protected function _importStreetFromAddress(Varien_Object $address, array &$to)
    {
        $keys = func_get_args(); array_shift($keys); array_shift($keys);
        $street = $address->getStreet();
        if (!$keys || !$street || !is_array($street)) {
            return;
        }
        foreach ($keys as $key) {
            if ($value = array_pop($street)) {
                $to[$key] = $value;
            }
        }
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
