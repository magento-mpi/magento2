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
 * Config model that is aware of all Mage_Paypal payment methods
 * Works with PayPal-specific system configuration
 */
class Mage_Paypal_Model_Config
{
    /**
     * PayPal Standard
     * @var string
     */
    const METHOD_WPS         = 'paypal_standard';

    /**
     * PayPal Website Payments Pro - Express Checkout
     * @var string
     */
    const METHOD_WPP_EXPRESS = 'paypal_express';

    /**
     * PayPal Website Payments Pro - Direct Payments
     * @var string
     */
    const METHOD_WPP_DIRECT  = 'paypal_direct';

    /**
     * Buttons and images
     * @var string
     */
    const EC_FLAVOR_DYNAMIC = 'dynamic';
    const EC_FLAVOR_STATIC  = 'static';
    const EC_BUTTON_TYPE_SHORTCUT = 'ecshortcut';
    const EC_BUTTON_TYPE_MARK     = 'ecmark';
    const PAYMENT_MARK_37x23   = '37x23';
    const PAYMENT_MARK_50x34   = '50x34';
    const PAYMENT_MARK_60x38   = '60x38';
    const PAYMENT_MARK_180x113 = '180x113';

    const DEFAULT_LOGO_TYPE = 'wePrefer_150x60';

    /**
     * Payment actions
     * @var string
     */
    const PAYMENT_ACTION_SALE  = 'Sale';
    const PAYMENT_ACTION_ORDER = 'Order';
    const PAYMENT_ACTION_AUTH  = 'Authorization';

    /**
     * Fraud management actions
     * @var string
     */
    const FRAUD_ACTION_ACCEPT = 'Acept';
    const FRAUD_ACTION_DENY   = 'Deny';

    /**
     * Refund types
     * @var string
     */
    const REFUND_TYPE_FULL = 'Full';
    const REFUND_TYPE_PARTIAL = 'Partial';

    /**
     * Express Checkout flows
     * @var string
     */
    const EC_SOLUTION_TYPE_SOLE = 'Sole';
    const EC_SOLUTION_TYPE_MARK = 'Mark';

    /**
     * Payment data transfer methods (Standard)
     *
     * @var string
     */
    const WPS_TRANSPORT_IPN      = 'ipn';
    const WPS_TRANSPORT_PDT      = 'pdt';
    const WPS_TRANSPORT_IPN_PDT  = 'ipn_n_pdt';

    /**
     * Default URL for centinel API (PayPal Direct)
     *
     * @var string
     */
    public $centinelDefaultApiUrl = 'https://paypal.cardinalcommerce.com/maps/txns.asp';

    /**
     * Current payment method code
     * @var string
     */
    protected $_methodCode = null;

    /**
     * Current store id
     * @var int
     */
    protected $_storeId = null;

    /**
     * Instructions for generating proper BN code
     *
     * @var array
     */
    protected $_buildNotationPPMap = array(
        'paypal_standard' => 'WPS',
        'paypal_express'  => 'EC',
        'paypal_direct'   => 'DP',
    );

    /**
     * Style system config map (Express Checkout)
     * @var array
     */
    protected $_ecStyleConfigMap = array(
        'page_style'    => 'page_style',
        'paypal_hdrimg' => 'hdrimg',
        'paypal_hdrbordercolor' => 'hdrbordercolor',
        'paypal_hdrbackcolor'   => 'hdrbackcolor',
        'paypal_payflowcolor'   => 'payflowcolor',
    );

    /**
     * Currency codes supported by PayPal methods
     * @var array
     */
    protected $_supportedCurrencyCodes = array('AUD', 'CAD', 'CZK', 'DKK', 'EUR', 'HKD', 'HUF', 'ILS', 'JPY', 'MXN',
        'NOK', 'NZD', 'PLN', 'GBP', 'SGD', 'SEK', 'CHF', 'USD');

    /**
     * Locale codes supported by misc images (marks, shortcuts etc)
     * @var array
     * @see https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_ECButtonIntegration#id089QD0O0TX4__id08AH904I0YK
     */
    protected $_supportedImageLocales = array('de_DE', 'en_AU', 'en_GB', 'en_US', 'es_ES', 'es_XC', 'fr_FR',
        'fr_XC', 'it_IT', 'ja_JP', 'nl_NL', 'pl_PL', 'zh_CN', 'zh_XC',
    );

    /**
     * Set method and store id, if specified
     * @param array $params
     */
    public function __construct($params = array())
    {
        if ($params) {
            $method = array_shift($params);
            $this->setMethod($method);
            if ($params) {
                $storeId = array_shift($params);
                $this->setStoreId($storeId);
            }
        }
    }

    /**
     * Method code setter
     *
     * @param string|Mage_Payment_Model_Method_Abstract $method
     * @return Mage_Paypal_Model_Config
     */
    public function setMethod($method)
    {
        if ($method instanceof Mage_Payment_Model_Method_Abstract) {
            $this->_methodCode = $method->getCode();
        } elseif (is_string($method)) {
            $this->_methodCode = $method;
        }
        return $this;
    }

    /**
     * Payment method instance code getter
     * @return string
     */
    public function getMethodCode()
    {
        return $this->_methodCode;
    }

    /**
     * Store ID setter
     * @param int $storeId
     * @return Mage_Paypal_Model_Config
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = (int)$storeId;
        return $this;
    }

    /**
     * Config field magic getter
     * The specified key can be either in camelCase or under_score format
     * Tries to map specified value according to set payment method code, into the configuration value
     * Sets the values into public class parameters, to avoid redundant calls of this method
     *
     * @param string $key
     * @return string|null
     */
    public function __get($key)
    {
        $underscored = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $key));
        $value = Mage::getStoreConfig($this->_getSpecificConfigPath($underscored), $this->_storeId);
        $this->$key = $value;
        $this->$underscored = $value;
        return $value;
    }

    /**
     * Return merchant country codes supported by PayPal
     *
     * @return array
     */
    public function getSupportedMerchantCountryCodes()
    {
        return explode(',', Mage::getStoreConfig('paypal/merchant_country_codes'));
    }

    /**
     * Get url for dispatching customer to express checkout start
     * @param string $token
     * @return string
     */
    public function getExpressCheckoutStartUrl($token)
    {
        return $this->getPaypalUrl(array(
            'cmd'   => '_express-checkout',
            'token' => $token,
        ));
    }

    /**
     * Get url that allows to edit checkout details on paypal side
     * @param $token
     * @return string
     */
    public function getExpressCheckoutEditUrl($token)
    {
        return $this->getPaypalUrl(array(
            'cmd'        => '_express-checkout',
            'useraction' => 'continue',
            'token'      => $token,
        ));
    }

    /**
     * Get url for additional actions that PayPal may require customer to do after placing the order.
     * For instance, redirecting customer to bank for payment confirmation.
     * @param string $token
     * @return string
     */
    public function getExpressCheckoutCompleteUrl($token)
    {
        return $this->getPaypalUrl(array(
            'cmd'   => '_complete-express-checkout',
            'token' => $token,
        ));
    }

     /**
     * PayPal web URL generic getter
     *
     * @param array $params
     * @return string
     */
    public function getPaypalUrl(array $params = array())
    {
        return sprintf('https://www.%spaypal.com/webscr%s',
            $this->sandboxFlag ? 'sandbox.' : '',
            $params ? '?' . http_build_query($params) : ''
        );
    }

    /**
     * Whether Express Checkout button should be rendered dynamically
     * @return bool
     */
    public function areButtonsDynamic()
    {
        return $this->buttonFlavor === self::EC_FLAVOR_DYNAMIC;
    }

    /**
     * Express checkout shortcut pic URL getter
     * PayPal will ignore "pal", if there is no total amount specified
     *
     * @param string $localeCode
     * @param float $orderTotal
     * @param string $pal encrypted summary about merchant
     * @see Paypal_Model_Api_Nvp::callGetPalDetails()
     */
    public function getExpressCheckoutShortcutImageUrl($localeCode, $orderTotal = null, $pal = null)
    {
        if ($this->areButtonsDynamic()) {
            return $this->_getDynamicImageUrl(self::EC_BUTTON_TYPE_SHORTCUT, $localeCode, $orderTotal, $pal);
        }
        if ($this->buttonType === self::EC_BUTTON_TYPE_MARK) {
            return $this->getPaymentMarkImageUrl($localeCode);
        }
        return sprintf('https://www.paypal.com/%s/i/btn/btn_xpressCheckout.gif',
            $this->_getSupportedLocaleCode($localeCode));
    }

    /**
     * Get PayPal "mark" image URL
     * Supposed to be used on payment methods selection
     * $staticSize is applicable for static images only
     *
     * @param string $localeCode
     * @param float $orderTotal
     * @param string $pal
     * @param string $staticSize
     */
    public function getPaymentMarkImageUrl($localeCode, $orderTotal = null, $pal = null, $staticSize = null)
    {
        if ($this->areButtonsDynamic()) {
            return $this->_getDynamicImageUrl(self::EC_BUTTON_TYPE_MARK, $localeCode, $orderTotal, $pal);
        }

        if (null === $staticSize) {
            $staticSize = $this->paymentMarkSize;
        }
        switch ($staticSize) {
            case self::PAYMENT_MARK_37x23:
            case self::PAYMENT_MARK_50x34:
            case self::PAYMENT_MARK_60x38:
            case self::PAYMENT_MARK_180x113:
                break;
            default:
                $staticSize = self::PAYMENT_MARK_37x23;
        }
        return sprintf('https://www.paypal.com/%s/i/logo/PayPal_mark_%s.gif',
            $this->_getSupportedLocaleCode($localeCode), $staticSize);
    }

    /**
     * Get "What Is PayPal" localized URL
     * Supposed to be used with "mark" as popup window
     *
     * @param Mage_Core_Model_Locale $locale
     */
    public function getPaymentMarkWhatIsPaypalUrl(Mage_Core_Model_Locale $locale = null)
    {
        $countryCode = 'US';
        if (null !== $locale) {
            $shouldEmulate = (null !== $this->_storeId) && (Mage::app()->getStore()->getId() != $this->_storeId);
            if ($shouldEmulate) {
                $locale->emulate($this->_storeId);
            }
            $countryCode = $locale->getLocale()->getRegion();
            if ($shouldEmulate) {
                $locale->revert();
            }
        }
        return sprintf('https://www.paypal.com/%s/cgi-bin/webscr?cmd=xpt/Marketing/popup/OLCWhatIsPayPal-outside',
            strtolower($countryCode)
        );
    }

    /**
     * Getter for Solution banner images
     *
     * @param string $localeCode
     * @param bool $isVertical
     * @param bool $isEcheck
     */
    public function getSolutionImageUrl($localeCode, $isVertical = false, $isEcheck = false)
    {
        return sprintf('https://www.paypal.com/%s/i/bnr/%s_solution_PP%s.gif',
            $this->_getSupportedLocaleCode($localeCode),
            $isVertical ? 'vertical' : 'horizontal', $isEcheck ? 'eCheck' : ''
        );
    }

    /**
     * Getter for Payment form logo images
     *
     * @param string $localeCode
     */
    public function getPaymentFormLogoUrl($localeCode)
    {
        $locale = $this->_getSupportedLocaleCode($localeCode);

        $imageType = 'logo';
        $domain = 'paypal.com';
        list (,$country) = explode('_', $locale);
        $countryPrefix = $country . '/';

        switch ($locale) {
            case 'en_GB':
                $imageName = 'horizontal_solution_PP';
                $imageType = 'bnr';
                $countryPrefix = '';
                break;
            case 'de_DE':
                $imageName = 'lockbox_150x47';
                break;
            case 'fr_FR':
                $imageName = 'bnr_horizontal_solution_PP_327wx80h';
                $imageType = 'bnr';
                $locale = 'en_US';
                $domain = 'paypalobjects.com';
                break;
            case 'it_IT':
                $imageName = 'bnr_horizontal_solution_PP_178wx80h';
                $imageType = 'bnr';
                $domain = 'paypalobjects.com';
                break;
            default:
                $imageName='PayPal_mark_60x38';
                $countryPrefix = '';
                break;
        }
        return sprintf('https://www.%s/%s/%si/%s/%s.gif', $domain, $locale, $countryPrefix, $imageType, $imageName);
    }

    /**
     * Return supported types for PayPal logo
     *
     * @return array
     */
    public function getAdditionalOptionsLogoTypes()
    {
        return array(
            'wePrefer_150x60'       => Mage::helper('paypal')->__('We prefer PayPal (150 X 60)'),
            'wePrefer_150x40'       => Mage::helper('paypal')->__('We prefer PayPal (150 X 40)'),
            'nowAccepting_150x60'   => Mage::helper('paypal')->__('Now accepting PayPal (150 X 60)'),
            'nowAccepting_150x40'   => Mage::helper('paypal')->__('Now accepting PayPal (150 X 40)'),
            'paymentsBy_150x60'     => Mage::helper('paypal')->__('Payments by PayPal (150 X 60)'),
            'paymentsBy_150x40'     => Mage::helper('paypal')->__('Payments by PayPal (150 X 40)'),
            'shopNowUsing_150x60'   => Mage::helper('paypal')->__('Shop now using (150 X 60)'),
            'shopNowUsing_150x40'   => Mage::helper('paypal')->__('Shop now using (150 X 40)'),
        );
    }

    /**
     * Return PayPal logo URL with additional options
     *
     * @param string $localeCode Supported locale code
     * @param string $type One of supported logo types
     * @return string|bool Logo Image URL or false if logo disabled in configuration
     */
    public function getAdditionalOptionsLogoUrl($localeCode, $type = false)
    {
        $configType = Mage::getStoreConfig($this->_mapGenericStyleFieldset('logo'), $this->_storeId);
        if (!$configType) {
            return false;
        }
        $type = $type ? $type : $configType;
        $locale = $this->_getSupportedLocaleCode($localeCode);
        $supportedTypes = array_keys($this->getAdditionalOptionsLogoTypes());
        if (!in_array($type, $supportedTypes)) {
            $type = self::DEFAULT_LOGO_TYPE;
        }
        return sprintf('https://www.paypalobjects.com/%s/i/bnr/bnr_%s.gif', $locale, $type);
    }

    /**
     * BN code getter
     *
     * @param string $countryCode ISO 3166-1
     */
    public function getBuildNotationCode($countryCode = null)
    {
        $product = 'WPP';
        if ($this->_methodCode && isset($this->_buildNotationPPMap[$this->_methodCode])) {
            $product = $this->_buildNotationPPMap[$this->_methodCode];
        }
        if (null === $countryCode) {
            $countryCode = $this->_matchBnCountryCode(Mage::getStoreConfig('general/country/default', $this->_storeId));
        }
        if ($countryCode) {
            $countryCode = '_' . $countryCode;
        }
        return sprintf('Varien_Cart_%s%s', $product, $countryCode);
    }

    /**
     * Express Checkout button "flavors" source getter
     * @return array
     */
    public function getExpressCheckoutButtonFlavors()
    {
        return array(
            self::EC_FLAVOR_DYNAMIC => Mage::helper('paypal')->__('Dynamic'),
            self::EC_FLAVOR_STATIC  => Mage::helper('paypal')->__('Static'),
        );
    }

    /**
     * Express Checkout button types source getter
     * @return array
     */
    public function getExpressCheckoutButtonTypes()
    {
        return array(
            self::EC_BUTTON_TYPE_SHORTCUT => Mage::helper('paypal')->__('Shortcut'),
            self::EC_BUTTON_TYPE_MARK     => Mage::helper('paypal')->__('Acceptance Mark Image'),
        );
    }

    /**
     * Payment actions source getter
     * @return array
     */
    public function getPaymentActions()
    {
        return array(
            self::PAYMENT_ACTION_AUTH  => Mage::helper('paypal')->__('Authorization'),
            // self::PAYMENT_ACTION_ORDER => Mage::helper('paypal')->__('Order'), // not supported yet
            self::PAYMENT_ACTION_SALE  => Mage::helper('paypal')->__('Sale'),
        );
    }

    /**
     * Mapper from PayPal-specific payment actions to Magento payment actions
     * @return string|null
     */
    public function getPaymentAction()
    {
        switch ($this->paymentAction) {
            case self::PAYMENT_ACTION_AUTH:
                return Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE;
            case self::PAYMENT_ACTION_SALE:
                return Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE;
            case self::PAYMENT_ACTION_ORDER:
                return;
        }
    }

    /**
     * Express Checkout "solution types" source getter
     * "sole" = "Express Checkout for Auctions" - PayPal allows guest checkout
     * "mark" = "Normal Express Checkout" - PayPal requires to checkout with PayPal buyer account only
     *
     * @return array
     */
    public function getExpressCheckoutSolutionTypes()
    {
        return array(
            self::EC_SOLUTION_TYPE_SOLE => Mage::helper('paypal')->__('Yes'),
            self::EC_SOLUTION_TYPE_MARK => Mage::helper('paypal')->__('No'),
        );
    }

    /**
     * Check whether only Unilateral payments (Accelerated Boarding) possible for Express method or not
     *
     * @return bool
     */
    public function shouldUseUnilateralPayments()
    {
        $email = Mage::getStoreConfig($this->_mapWppFieldset('business_account'), $this->_storeId);
        $apiUser = Mage::getStoreConfig($this->_mapWppFieldset('api_username'), $this->_storeId);
        $apiPassword = Mage::getStoreConfig($this->_mapWppFieldset('api_password'), $this->_storeId);
        $apiSignature = Mage::getStoreConfig($this->_mapWppFieldset('api_signature'), $this->_storeId);
        return $email && (!$apiUser || !$apiPassword || !$apiSignature);
    }

    /**
     * Payment data delivery methods getter for PayPal Standard
     * @return array
     */
    public function getWpsPaymentDeliveryMethods()
    {
        return array(
            self::WPS_TRANSPORT_IPN      => Mage::helper('adminhtml')->__('IPN (Instant Payment Notification) Only'),
            // not supported yet:
//            self::WPS_TRANSPORT_PDT      => Mage::helper('adminhtml')->__('PDT (Payment Data Transfer) Only'),
//            self::WPS_TRANSPORT_IPN_PDT  => Mage::helper('adminhtml')->__('Both IPN and PDT'),
        );
    }

    /**
     * PayPal Direct cc types source getter
     *
     * @return array
     */
    public function getDirectCcTypesAsOptionArray()
    {
        $model = Mage::getModel('payment/source_cctype')->setAllowedTypes(array('VI', 'MC', 'AE', 'DI', 'OT'));
        return $model->toOptionArray();
    }

    /**
     * Check whether specified currency code is supported
     * @param string $code
     * @return bool
     */
    public function isCurrencyCodeSupported($code)
    {
        return in_array($code, $this->_supportedCurrencyCodes);
    }

    /**
     * Export page style current settings to specified object
     * @param Varien_Object $to
     */
    public function exportExpressCheckoutStyleSettings(Varien_Object $to)
    {
        foreach ($this->_ecStyleConfigMap as $key => $exportKey) {
            if ($this->$key) {
                $to->setData($exportKey, $this->$key);
            }
        }
    }

    /**
     * Dynamic PayPal image URL getter
     * Also can render dynamic Acceptance Mark
     *
     * @param string $type
     * @param string $localeCode
     * @param float $orderTotal
     * @param string $pal
     */
    protected function _getDynamicImageUrl($type, $localeCode, $orderTotal, $pal)
    {
        $params = array(
            'cmd'        => '_dynamic-image',
            'buttontype' => $type,
            'locale'     => $this->_getSupportedLocaleCode($localeCode),
        );
        if ($orderTotal) {
            $params['ordertotal'] = sprintf('%.2F', $orderTotal);
            if ($pal) {
                $params['pal'] = $pal;
            }
        }
        return sprintf('https://fpdbs%s.paypal.com/dynamicimageweb?%s',
            $this->sandboxFlag ? '.sandbox' : '', http_build_query($params)
        );
    }

    /**
     * Check whether specified locale code is supported. Fallback to en_US
     *
     * @param string $localeCode
     * @return string
     */
    protected function _getSupportedLocaleCode($localeCode = null)
    {
        if (!$localeCode || !in_array($localeCode, $this->_supportedImageLocales)) {
            return 'en_US';
        }
        return $localeCode;
    }

    /**
     * Map any supported payment method into a config path by specified field name
     * @param string $fieldName
     * @return string|null
     */
    protected function _getSpecificConfigPath($fieldName)
    {
        if (self::METHOD_WPS === $this->_methodCode) {
            return $this->_mapStandardFieldset($fieldName);
        } elseif (self::METHOD_WPP_EXPRESS === $this->_methodCode ||  self::METHOD_WPP_DIRECT === $this->_methodCode) {
            $path = self::METHOD_WPP_EXPRESS === $this->_methodCode
                ? $this->_mapExpressFieldset($fieldName)
                : $this->_mapDirectFieldset($fieldName)
             ;
            if (!$path) {
                $path = $this->_mapWppFieldset($fieldName);
            }
            if (!$path) {
                $path = $this->_mapGenericStyleFieldset($fieldName);
            }
            return $path;
        }
    }

    /**
     * Map PayPal Standard config fields
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _mapStandardFieldset($fieldName)
    {
        switch ($fieldName)
        {
            case 'business_account':
            case 'allowspecific':
            case 'specificcountry':
            case 'line_items_enabled':
            case 'line_items_summary':
                return "paypal/general/{$fieldName}";
            case 'debug_flag':
            case 'sandbox_flag':
                return "paypal/wps/{$fieldName}";
            case 'active':
            case 'title':
            case 'payment_action':
            case 'sort_order':
                return 'payment/' . self::METHOD_WPS . "/{$fieldName}";
            default:
                return $this->_mapGenericStyleFieldset($fieldName);
        }
    }

    /**
     * Check wheter specified country code is supported by build notation codes for specific countries
     *
     * @param $code
     * @return string|null
     */
    private function _matchBnCountryCode($code)
    {
        switch ($code) {
            // Australia, Austria, Belgium, Canada, China, France, Germany, Hong Kong, Italy
            case 'AU': case 'AT': case 'BE': case 'CA': case 'CN': case 'FR': case 'DE': case 'HK': case 'IT':
            // Japan, Mexico, Netherlands, Poland, Singapore, Spain, Switzerland, United Kingdom, United States
            case 'JP': case 'MX': case 'NL': case 'PL': case 'SG': case 'ES': case 'CH': case 'UK': case 'US':
                return $code;
        }
    }

    /**
     * Map PayPal common style config fields
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _mapGenericStyleFieldset($fieldName)
    {
        switch ($fieldName) {
            case 'logo':
            case 'page_style':
            case 'paypal_hdrimg':
            case 'paypal_hdrbackcolor':
            case 'paypal_hdrbordercolor':
            case 'paypal_payflowcolor':
            case 'button_flavor':
                return "paypal/style/{$fieldName}";
        }
    }

    /**
     * Map PayPal Website Payments Pro common config fields
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _mapWppFieldset($fieldName)
    {
        switch ($fieldName)
        {
            case 'business_account':
                return "paypal/general/{$fieldName}";
            case 'api_password':
            case 'api_signature':
            case 'api_username':
            case 'debug_flag':
            case 'paypal_url':
            case 'proxy_host':
            case 'proxy_port':
            case 'sandbox_flag':
            case 'use_proxy':
                return "paypal/wpp/{$fieldName}";
        }
    }

    /**
     * Map PayPal Express config fields
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _mapExpressFieldset($fieldName)
    {
        switch ($fieldName)
        {
            case 'allowspecific':
            case 'specificcountry':
            case 'line_items_enabled':
            case 'use_payflow':
                return "paypal/general/{$fieldName}";
            case 'active':
            case 'fraud_filter':
            case 'payment_action':
            case 'solution_type':
            case 'sort_order':
            case 'title':
            case 'visible_on_cart':
            case 'sandbox_flag':
            case 'debug_flag':
                return 'payment/' . self::METHOD_WPP_EXPRESS . "/{$fieldName}";
        }
    }

    /**
     * Map PayPal Direct config fields
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _mapDirectFieldset($fieldName)
    {
        switch ($fieldName)
        {
            case 'allowspecific':
            case 'specificcountry':
            case 'line_items_enabled':
            case 'use_payflow':
                return "paypal/general/{$fieldName}";
            case 'active':
            case 'cctypes':
            case 'centinel':
            case 'centinel_is_mode_strict':
            case 'centinel_api_url':
            case 'fraud_filter':
            case 'payment_action':
            case 'sort_order':
            case 'title':
                return 'payment/' . self::METHOD_WPP_DIRECT . "/{$fieldName}";
        }
    }
}
