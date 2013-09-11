<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Store model
 *
 * @method Magento_Core_Model_Resource_Store _getResource()
 * @method Magento_Core_Model_Resource_Store getResource()
 * @method Magento_Core_Model_Store setId(string $value)
 * @method Magento_Core_Model_Store setCode(string $value)
 * @method Magento_Core_Model_Store setWebsiteId(int $value)
 * @method Magento_Core_Model_Store setGroupId(int $value)
 * @method Magento_Core_Model_Store setName(string $value)
 * @method int getSortOrder()
 * @method int getStoreId()
 * @method Magento_Core_Model_Store setSortOrder(int $value)
 * @method Magento_Core_Model_Store setIsActive(int $value)
 */
class Magento_Core_Model_Store extends Magento_Core_Model_Abstract
{
    /**
     * Entity name
     */
    const ENTITY = 'core_store';

    /**
     * Custom entry point param
     */
    const CUSTOM_ENTRY_POINT_PARAM = 'custom_entry_point';

    /**#@+
     * Configuration paths
     */
    const XML_PATH_STORE_STORE_NAME         = 'general/store_information/name';
    const XML_PATH_STORE_STORE_PHONE        = 'general/store_information/phone';
    const XML_PATH_STORE_IN_URL             = 'web/url/use_store';
    const XML_PATH_USE_REWRITES             = 'web/seo/use_rewrites';
    const XML_PATH_UNSECURE_BASE_URL        = 'web/unsecure/base_url';
    const XML_PATH_SECURE_BASE_URL          = 'web/secure/base_url';
    const XML_PATH_SECURE_IN_FRONTEND       = 'web/secure/use_in_frontend';
    const XML_PATH_SECURE_IN_ADMINHTML      = 'web/secure/use_in_adminhtml';
    const XML_PATH_SECURE_BASE_LINK_URL     = 'web/secure/base_link_url';
    const XML_PATH_UNSECURE_BASE_LINK_URL   = 'web/unsecure/base_link_url';
    const XML_PATH_SECURE_BASE_LIB_URL      = 'web/secure/base_lib_url';
    const XML_PATH_UNSECURE_BASE_LIB_URL    = 'web/unsecure/base_lib_url';
    const XML_PATH_SECURE_BASE_STATIC_URL   = 'web/secure/base_static_url';
    const XML_PATH_UNSECURE_BASE_STATIC_URL = 'web/unsecure/base_static_url';
    const XML_PATH_SECURE_BASE_CACHE_URL    = 'web/secure/base_cache_url';
    const XML_PATH_UNSECURE_BASE_CACHE_URL  = 'web/unsecure/base_cache_url';
    const XML_PATH_SECURE_BASE_MEDIA_URL    = 'web/secure/base_media_url';
    const XML_PATH_UNSECURE_BASE_MEDIA_URL  = 'web/unsecure/base_media_url';
    const XML_PATH_OFFLOADER_HEADER         = 'web/secure/offloader_header';
    const XML_PATH_PRICE_SCOPE              = 'catalog/price/scope';
    /**#@- */

    /**
     * Price scope constants
     */
    const PRICE_SCOPE_GLOBAL              = 0;
    const PRICE_SCOPE_WEBSITE             = 1;

    /**#@+
     * Possible URL types
     */
    const URL_TYPE_LINK                   = 'link';
    const URL_TYPE_DIRECT_LINK            = 'direct_link';
    const URL_TYPE_WEB                    = 'web';
    const URL_TYPE_LIB                    = 'lib';
    const URL_TYPE_MEDIA                  = 'media';
    const URL_TYPE_STATIC                 = 'static';
    const URL_TYPE_CACHE                  = 'cache';
    /**#@-*/

    /**
     * Code constants
     */
    const DEFAULT_CODE                    = 'default';
    const ADMIN_CODE                      = 'admin';

    /**
     * Cache tag
     */
    const CACHE_TAG                       = 'store';

    /**
     * Cookie name
     */
    const COOKIE_NAME                     = 'store';

    /**
     * Cookie currency key
     */
    const COOKIE_CURRENCY                 = 'currency';

    /**
     * Script name, which returns all the images
     */
    const MEDIA_REWRITE_SCRIPT            = 'get.php/';

    /**
     * A placeholder for generating base URL
     */
    const BASE_URL_PLACEHOLDER            = '{{base_url}}';

    /**
     * @var Magento_Core_Model_Cache_Type_Config
     */
    protected $_configCacheType;

    /**
     * Cache flag
     *
     * @var boolean
     */
    protected $_cacheTag    = true;

    /**
     * Event prefix for model events
     *
     * @var string
     */
    protected $_eventPrefix = 'store';

    /**
     * Event object name
     *
     * @var string
     */
    protected $_eventObject = 'store';

    /**
     * Price filter
     *
     * @var Magento_Directory_Model_Currency_Filter
     */
    protected $_priceFilter;

    /**
     * Group model
     *
     * @var Magento_Core_Model_Store_Group
     */
    protected $_group;

    /**
     * Store configuration cache
     *
     * @var array|null
     */
    protected $_configCache = null;

    /**
     * Base nodes of store configuration cache
     *
     * @var array
     */
    protected $_configCacheBaseNodes = array();

    /**
     * Directory cache
     *
     * @var array
     */
    protected $_dirCache = array();

    /**
     * URL cache
     *
     * @var array
     */
    protected $_urlCache = array();

    /**
     * Base URL cache
     *
     * @var array
     */
    protected $_baseUrlCache = array();

    /**
     * Session entity
     *
     * @var Magento_Core_Model_Session_Abstract
     */
    protected $_session;

    /**
     * Flag that shows that backend URLs are secure
     *
     * @var boolean|null
     */
    protected $_isAdminSecure = null;

    /**
     * Flag that shows that frontend URLs are secure
     *
     * @var boolean|null
     */
    protected $_isFrontSecure = null;

    /**
     * Store frontend name
     *
     * @var string|null
     */
    protected $_frontendName = null;

    /**
     * Readonly flag
     *
     * @var bool
     */
    private $_isReadOnly = false;

    /**
     * Url model for current store
     *
     * @var Magento_Core_Model_UrlInterface
     */
    protected $_urlModel;

    /**
     * @var Magento_Core_Model_App_State
     */
    protected $_appState;

    /**
     * @var bool
     */
    protected $_isCustomEntryPoint = false;

    /**
     * @var Magento_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * @var Magento_Core_Model_Resource_Config_Data
     */
    protected $_configDataResource;

    /**
     * @var Magento_Core_Model_Dir
     */
    protected $_dir;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Cache_Type_Config $configCacheType
     * @param Magento_Core_Model_Url $urlModel
     * @param Magento_Core_Model_App_State $appState
     * @param Magento_Core_Controller_Request_Http $request
     * @param Magento_Core_Model_Resource_Config_Data $configDataResource
     * @param Magento_Core_Model_Dir $dir
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param bool $isCustomEntryPoint
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Cache_Type_Config $configCacheType,
        Magento_Core_Model_Url $urlModel,
        Magento_Core_Model_App_State $appState,
        Magento_Core_Controller_Request_Http $request,
        Magento_Core_Model_Resource_Config_Data $configDataResource,
        Magento_Core_Model_Dir $dir,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        $isCustomEntryPoint = false,
        array $data = array()
    ) {
        $this->_urlModel = $urlModel;
        $this->_configCacheType = $configCacheType;
        $this->_appState = $appState;
        $this->_request = $request;
        $this->_configDataResource = $configDataResource;
        $this->_isCustomEntryPoint = $isCustomEntryPoint;
        $this->_dir = $dir;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize object
     */
    protected function _construct()
    {
        $this->_init('Magento_Core_Model_Resource_Store');
    }

    /**
     * Retrieve store session object
     *
     * @return Magento_Core_Model_Session_Abstract
     */
    protected function _getSession()
    {
        if (!$this->_session) {
            $this->_session = Mage::getModel('Magento_Core_Model_Session')
                ->init('store_'.$this->getCode());
        }
        return $this->_session;
    }

    /**
     * Validation rules for store
     *
     * @return Zend_Validate_Interface|null
     */
    protected function _getValidationRulesBeforeSave()
    {
        $validator = new Magento_Validator_Composite_VarienObject();

        $storeLabelRule = new Zend_Validate_NotEmpty();
        $storeLabelRule->setMessage(
            __('Name is required'),
            Zend_Validate_NotEmpty::IS_EMPTY
        );
        $validator->addRule($storeLabelRule, 'name');

        $storeCodeRule = new Zend_Validate_Regex('/^[a-z]+[a-z0-9_]*$/');
        $storeCodeRule->setMessage(
            __('The store code may contain only letters (a-z), numbers (0-9) or underscore(_), the first character must be a letter'),
            Zend_Validate_Regex::NOT_MATCH
        );
        $validator->addRule($storeCodeRule, 'code');

        return $validator;
    }

    /**
     * Loading store data
     *
     * @param   mixed $key
     * @param   string $field
     * @return  Magento_Core_Model_Store
     */
    public function load($key, $field = null)
    {
        if (!is_numeric($key) && is_null($field)) {
            $this->_getResource()->load($this, $key, 'code');
            return $this;
        }
        return parent::load($key, $field);
    }

    /**
     * Retrieve Store code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->_getData('code');
    }

    /**
     * Retrieve store configuration data
     *
     * @param   string $path
     * @return  string|null
     */
    public function getConfig($path)
    {
        /** @var $config Magento_Core_Model_Config */
        $config = Mage::getSingleton('Magento_Core_Model_Config');
        $data = $config->getValue($path, 'store', $this->getCode());
        if (!$data && !$this->_appState->isInstalled()) {
            $data = $config->getValue($path, 'default');
        }
        if ($data === false) {
            return null;
        }
        return $data;
    }

    /**
     * Set config value for CURRENT model
     *
     * This value don't save in config
     *
     * @param string $path
     * @param mixed $value
     * @return Magento_Core_Model_Store
     */
    public function setConfig($path, $value)
    {
        Mage::getConfig()->setValue($path, $value, 'store', $this->getCode());
        return $this;
    }

    /**
     * Set relation to the website
     *
     * @param Magento_Core_Model_Website $website
     */
    public function setWebsite(Magento_Core_Model_Website $website)
    {
        $this->setWebsiteId($website->getId());
    }

    /**
     * Retrieve store website
     *
     * @return Magento_Core_Model_Website|bool
     */
    public function getWebsite()
    {
        if (is_null($this->getWebsiteId())) {
            return false;
        }
        return Mage::app()->getWebsite($this->getWebsiteId());
    }

    /**
     * Retrieve url using store configuration specific
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = array())
    {
        /** @var $url Magento_Core_Model_Url */
        $url = $this->getUrlModel()
            ->setStore($this);
        if (Mage::app()->getStore()->getId() != $this->getId()) {
            $params['_store_to_url'] = true;
        }

        return $url->getUrl($route, $params);
    }

    /**
     * Retrieve base URL
     *
     * @param string $type
     * @param boolean|null $secure
     * @return string
     * @throws InvalidArgumentException
     */
    public function getBaseUrl($type = self::URL_TYPE_LINK, $secure = null)
    {
        $cacheKey = $type . '/' . (is_null($secure) ? 'null' : ($secure ? 'true' : 'false'));
        if (!isset($this->_baseUrlCache[$cacheKey])) {
            $secure = is_null($secure) ? $this->isCurrentlySecure() : (bool)$secure;
            switch ($type) {
                case self::URL_TYPE_WEB:
                    $path = $secure ? self::XML_PATH_SECURE_BASE_URL : self::XML_PATH_UNSECURE_BASE_URL;
                    $url = $this->getConfig($path);
                    break;

                case self::URL_TYPE_LINK:
                    $path = $secure ? self::XML_PATH_SECURE_BASE_LINK_URL : self::XML_PATH_UNSECURE_BASE_LINK_URL;
                    $url = $this->getConfig($path);
                    $url = $this->_updatePathUseRewrites($url);
                    $url = $this->_updatePathUseStoreView($url);
                    break;

                case self::URL_TYPE_DIRECT_LINK:
                    $path = $secure ? self::XML_PATH_SECURE_BASE_LINK_URL : self::XML_PATH_UNSECURE_BASE_LINK_URL;
                    $url = $this->getConfig($path);
                    $url = $this->_updatePathUseRewrites($url);
                    break;

                case self::URL_TYPE_LIB:
                    $path = $secure ? self::XML_PATH_SECURE_BASE_LIB_URL : self::XML_PATH_UNSECURE_BASE_LIB_URL;
                    $url = $this->getConfig($path);
                    if (!$url) {
                        $url = $this->getBaseUrl(self::URL_TYPE_WEB, $secure)
                            . $this->_dir->getUri(Magento_Core_Model_Dir::PUB_LIB);
                    }
                    break;

                case self::URL_TYPE_STATIC:
                    $path = $secure ? self::XML_PATH_SECURE_BASE_STATIC_URL : self::XML_PATH_UNSECURE_BASE_STATIC_URL;
                    $url = $this->getConfig($path);
                    if (!$url) {
                        $url = $this->getBaseUrl(self::URL_TYPE_WEB, $secure)
                            . $this->_dir->getUri(Magento_Core_Model_Dir::STATIC_VIEW);
                    }
                    break;

                case self::URL_TYPE_CACHE:
                    $path = $secure ? self::XML_PATH_SECURE_BASE_CACHE_URL : self::XML_PATH_UNSECURE_BASE_CACHE_URL;
                    $url = $this->getConfig($path);
                    if (!$url) {
                        $url = $this->getBaseUrl(self::URL_TYPE_WEB, $secure)
                            . $this->_dir->getUri(Magento_Core_Model_Dir::PUB_VIEW_CACHE);
                    }
                    break;

                case self::URL_TYPE_MEDIA:
                    $url = $this->_getMediaScriptUrl($this->_dir, $secure);
                    if (!$url) {
                        $path = $secure ? self::XML_PATH_SECURE_BASE_MEDIA_URL : self::XML_PATH_UNSECURE_BASE_MEDIA_URL;
                        $url = $this->getConfig($path);
                        if (!$url) {
                            $url = $this->getBaseUrl(self::URL_TYPE_WEB, $secure)
                                . $this->_dir->getUri(Magento_Core_Model_Dir::MEDIA);
                        }
                    }
                    break;

                default:
                    throw new InvalidArgumentException('Invalid base url type');
            }

            if (false !== strpos($url, Magento_Core_Model_Store::BASE_URL_PLACEHOLDER)) {
                $distroBaseUrl = $this->_request->getDistroBaseUrl();
                $url = str_replace(Magento_Core_Model_Store::BASE_URL_PLACEHOLDER, $distroBaseUrl, $url);
            }

            $this->_baseUrlCache[$cacheKey] = rtrim($url, '/') . '/';
        }

        return $this->_baseUrlCache[$cacheKey];
    }

    /**
     * Remove script file name from url in case when server rewrites are enabled
     *
     * @param   string $url
     * @return  string
     */
    protected function _updatePathUseRewrites($url)
    {
        if ($this->isAdmin()
            || !$this->getConfig(self::XML_PATH_USE_REWRITES)
            || !$this->_appState->isInstalled()
        ) {
            if ($this->_isCustomEntryPoint()) {
                $indexFileName = 'index.php';
            } else {
                $indexFileName = basename($_SERVER['SCRIPT_FILENAME']);
            }
            $url .= $indexFileName . '/';
        }
        return $url;
    }

    /**
     * Check if used entry point is custom
     *
     * @return bool
     */
    protected function _isCustomEntryPoint()
    {
        return $this->_isCustomEntryPoint;
    }

    /**
     * Retrieve URL for media catalog
     *
     * If we use Database file storage and server doesn't support rewrites (.htaccess in media folder)
     * we have to put name of fetching media script exactly into URL
     *
     * @param Magento_Core_Model_Dir $dirs
     * @param bool $secure
     * @return string|bool
     */
    protected function _getMediaScriptUrl(Magento_Core_Model_Dir $dirs, $secure)
    {
        if (!$this->getConfig(self::XML_PATH_USE_REWRITES)
            && Mage::helper('Magento_Core_Helper_File_Storage_Database')->checkDbUsage()
        ) {
            return $this->getBaseUrl(self::URL_TYPE_WEB, $secure) . $dirs->getUri(Magento_Core_Model_Dir::PUB)
                . '/' . self::MEDIA_REWRITE_SCRIPT;
        }
        return false;
    }

    /**
     * Add store code to url in case if it is enabled in configuration
     *
     * @param   string $url
     * @return  string
     */
    protected function _updatePathUseStoreView($url)
    {
        if ($this->isUseStoreInUrl()) {
            $url .= $this->getCode() . '/';
        }
        return $url;
    }

    /**
     * Returns whether url forming scheme prepends url path with store view code
     *
     * @return boolean
     */
    public function isUseStoreInUrl()
    {
        return $this->_appState->isInstalled()
            && $this->getConfig(self::XML_PATH_STORE_IN_URL)
            && !$this->isAdmin();
    }

    /**
     * Get store identifier
     *
     * @return  int
     */
    public function getId()
    {
        return $this->_getData('store_id');
    }

    /**
     * Check if store is admin store
     *
     * @return boolean
     */
    public function isAdmin()
    {
        return $this->getId() == Magento_Core_Model_AppInterface::ADMIN_STORE_ID;
    }


    /**
     * Check if backend URLs should be secure
     *
     * @return boolean
     */
    public function isAdminUrlSecure()
    {
        if ($this->_isAdminSecure === null) {
            $this->_isAdminSecure = (boolean) (int) (string) Mage::getConfig()
                ->getValue(Magento_Core_Model_Url::XML_PATH_SECURE_IN_ADMIN, 'default');
        }
        return $this->_isAdminSecure;
    }

    /**
     * Check if frontend URLs should be secure
     *
     * @return boolean
     */
    public function isFrontUrlSecure()
    {
        if ($this->_isFrontSecure === null) {
            $this->_isFrontSecure = Mage::getStoreConfigFlag(Magento_Core_Model_Url::XML_PATH_SECURE_IN_FRONT,
                $this->getId());
        }
        return $this->_isFrontSecure;
    }

    /**
     * Check if request was secure
     *
     * @return boolean
     */
    public function isCurrentlySecure()
    {
        $standardRule = !empty($_SERVER['HTTPS']) && ('off' != $_SERVER['HTTPS']);
        $offloaderHeader = trim((string) Mage::getConfig()->getValue(self::XML_PATH_OFFLOADER_HEADER, 'default'));

        if ((!empty($offloaderHeader) && !empty($_SERVER[$offloaderHeader])) || $standardRule) {
            return true;
        }

        if ($this->_appState->isInstalled()) {
            $secureBaseUrl = Mage::getStoreConfig(Magento_Core_Model_Url::XML_PATH_SECURE_URL);

            if (!$secureBaseUrl) {
                return false;
            }

            $uri = Zend_Uri::factory($secureBaseUrl);
            $port = $uri->getPort();
            $isSecure = ($uri->getScheme() == 'https')
                && isset($_SERVER['SERVER_PORT'])
                && ($port == $_SERVER['SERVER_PORT']);
            return $isSecure;
        } else {
            $isSecure = isset($_SERVER['SERVER_PORT']) && (443 == $_SERVER['SERVER_PORT']);
            return $isSecure;
        }
    }

    /*************************************************************************************
     * Store currency interface
     */

    /**
     * Retrieve store base currency code
     *
     * @return string
     */
    public function getBaseCurrencyCode()
    {
        $configValue = $this->getConfig(Magento_Core_Model_Store::XML_PATH_PRICE_SCOPE);
        if ($configValue == Magento_Core_Model_Store::PRICE_SCOPE_GLOBAL) {
            return Mage::app()->getBaseCurrencyCode();
        } else {
            return $this->getConfig(Magento_Directory_Model_Currency::XML_PATH_CURRENCY_BASE);
        }
    }

    /**
     * Retrieve store base currency
     *
     * @return Magento_Directory_Model_Currency
     */
    public function getBaseCurrency()
    {
        $currency = $this->getData('base_currency');
        if (is_null($currency)) {
            $currency = Mage::getModel('Magento_Directory_Model_Currency')->load($this->getBaseCurrencyCode());
            $this->setData('base_currency', $currency);
        }
        return $currency;
    }

    /**
     * Get default store currency code
     *
     * @return string
     */
    public function getDefaultCurrencyCode()
    {
        $result = $this->getConfig(Magento_Directory_Model_Currency::XML_PATH_CURRENCY_DEFAULT);
        return $result;
    }

    /**
     * Retrieve store default currency
     *
     * @return Magento_Directory_Model_Currency
     */
    public function getDefaultCurrency()
    {
        $currency = $this->getData('default_currency');
        if (is_null($currency)) {
            $currency = Mage::getModel('Magento_Directory_Model_Currency')->load($this->getDefaultCurrencyCode());
            $this->setData('default_currency', $currency);
        }
        return $currency;
    }

    /**
     * Set current store currency code
     *
     * @param   string $code
     * @return  string
     */
    public function setCurrentCurrencyCode($code)
    {
        $code = strtoupper($code);
        if (in_array($code, $this->getAvailableCurrencyCodes())) {
            $this->_getSession()->setCurrencyCode($code);
            if ($code == $this->getDefaultCurrency()) {
                Mage::app()->getCookie()->delete(self::COOKIE_CURRENCY, $code);
            } else {
                Mage::app()->getCookie()->set(self::COOKIE_CURRENCY, $code);
            }
        }
        return $this;
    }

    /**
     * Get current store currency code
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        // try to get currently set code among allowed
        $code = $this->_getSession()->getCurrencyCode();
        if (empty($code)) {
            $code = $this->getDefaultCurrencyCode();
        }
        if (in_array($code, $this->getAvailableCurrencyCodes(true))) {
            return $code;
        }

        // take first one of allowed codes
        $codes = array_values($this->getAvailableCurrencyCodes(true));
        if (empty($codes)) {
            // return default code, if no codes specified at all
            return $this->getDefaultCurrencyCode();
        }
        return array_shift($codes);
    }

    /**
     * Get allowed store currency codes
     *
     * If base currency is not allowed in current website config scope,
     * then it can be disabled with $skipBaseNotAllowed
     *
     * @param bool $skipBaseNotAllowed
     * @return array
     */
    public function getAvailableCurrencyCodes($skipBaseNotAllowed = false)
    {
        $codes = $this->getData('available_currency_codes');
        if (is_null($codes)) {
            $codes = explode(',', $this->getConfig(Magento_Directory_Model_Currency::XML_PATH_CURRENCY_ALLOW));
            // add base currency, if it is not in allowed currencies
            $baseCurrencyCode = $this->getBaseCurrencyCode();
            if (!in_array($baseCurrencyCode, $codes)) {
                $codes[] = $baseCurrencyCode;

                // save base currency code index for further usage
                $disallowedBaseCodeIndex = array_keys($codes);
                $disallowedBaseCodeIndex = array_pop($disallowedBaseCodeIndex);
                $this->setData('disallowed_base_currency_code_index', $disallowedBaseCodeIndex);
            }
            $this->setData('available_currency_codes', $codes);
        }

        // remove base currency code, if it is not allowed by config (optional)
        if ($skipBaseNotAllowed) {
            $disallowedBaseCodeIndex = $this->getData('disallowed_base_currency_code_index');
            if (null !== $disallowedBaseCodeIndex) {
                unset($codes[$disallowedBaseCodeIndex]);
            }
        }
        return $codes;
    }

    /**
     * Retrieve store current currency
     *
     * @return Magento_Directory_Model_Currency
     */
    public function getCurrentCurrency()
    {
        $currency = $this->getData('current_currency');

        if (is_null($currency)) {
            $currency     = Mage::getModel('Magento_Directory_Model_Currency')->load($this->getCurrentCurrencyCode());
            $baseCurrency = $this->getBaseCurrency();

            if (! $baseCurrency->getRate($currency)) {
                $currency = $baseCurrency;
                $this->setCurrentCurrencyCode($baseCurrency->getCode());
            }

            $this->setData('current_currency', $currency);
        }

        return $currency;
    }

    /**
     * Retrieve current currency rate
     *
     * @return float
     */
    public function getCurrentCurrencyRate()
    {
        return $this->getBaseCurrency()->getRate($this->getCurrentCurrency());
    }

    /**
     * Convert price from default currency to current currency
     *
     * @param   double $price
     * @param   boolean $format             Format price to currency format
     * @param   boolean $includeContainer   Enclose into <span class="price"><span>
     * @return  double
     */
    public function convertPrice($price, $format = false, $includeContainer = true)
    {
        if ($this->getCurrentCurrency() && $this->getBaseCurrency()) {
            $value = $this->getBaseCurrency()->convert($price, $this->getCurrentCurrency());
        } else {
            $value = $price;
        }

        if ($this->getCurrentCurrency() && $format) {
            $value = $this->formatPrice($value, $includeContainer);
        }
        return $value;
    }

    /**
     * Round price
     *
     * @param mixed $price
     * @return double
     */
    public function roundPrice($price)
    {
        return round($price, 2);
    }

    /**
     * Format price with currency filter (taking rate into consideration)
     *
     * @param   double $price
     * @param   bool $includeContainer
     * @return  string
     */
    public function formatPrice($price, $includeContainer = true)
    {
        if ($this->getCurrentCurrency()) {
            return $this->getCurrentCurrency()->format($price, array(), $includeContainer);
        }
        return $price;
    }

    /**
     * Get store price filter
     *
     * @return Magento_Filter_Sprintf
     */
    public function getPriceFilter()
    {
        if (!$this->_priceFilter) {
            if ($this->getBaseCurrency() && $this->getCurrentCurrency()) {
                $this->_priceFilter = $this->getCurrentCurrency()->getFilter();
                $this->_priceFilter->setRate($this->getBaseCurrency()->getRate($this->getCurrentCurrency()));
            } elseif ($this->getDefaultCurrency()) {
                $this->_priceFilter = $this->getDefaultCurrency()->getFilter();
            } else {
                $this->_priceFilter = new Magento_Filter_Sprintf('%s', 2);
            }
        }
        return $this->_priceFilter;
    }

    /**
     * Retrieve root category identifier
     *
     * @return int
     */
    public function getRootCategoryId()
    {
        if (!$this->getGroup()) {
            return 0;
        }
        return $this->getGroup()->getRootCategoryId();
    }

    /**
     * Set group model for store
     *
     * @param Magento_Core_Model_Store_Group $group
     */
    public function setGroup($group)
    {
        $this->setGroupId($group->getId());
    }

    /**
     * Retrieve group model
     *
     * @return Magento_Core_Model_Store_Group|bool
     */
    public function getGroup()
    {
        if (is_null($this->getGroupId())) {
            return false;
        }
        return Mage::app()->getGroup($this->getGroupId());
    }

    /**
     * Retrieve website identifier
     *
     * @return string|int|null
     */
    public function getWebsiteId()
    {
        return $this->_getData('website_id');
    }

    /**
     * Retrieve group identifier
     *
     * @return string|int|null
     */
    public function getGroupId()
    {
        return $this->_getData('group_id');
    }

    /**
     * Retrieve default group identifier
     *
     * @return string|int|null
     */
    public function getDefaultGroupId()
    {
        return $this->_getData('default_group_id');
    }

    /**
     * Check if store can be deleted
     *
     * @return boolean
     */
    public function isCanDelete()
    {
        if (!$this->getId()) {
            return false;
        }

        return $this->getGroup()->getDefaultStoreId() != $this->getId();
    }

    /**
     * Retrieve current url for store
     *
     * @param bool|string $fromStore
     * @return string
     */
    public function getCurrentUrl($fromStore = true)
    {
        $sidQueryParam = $this->_getSession()->getSessionIdQueryParam();
        $requestString = $this->getUrlModel()->escape(
            ltrim(Mage::app()->getRequest()->getRequestString(), '/'));

        $storeUrl = Mage::app()->getStore()->isCurrentlySecure()
            ? $this->getUrl('', array('_secure' => true))
            : $this->getUrl('');

        if (!filter_var($storeUrl, FILTER_VALIDATE_URL)) {
            return $storeUrl;
        }

        $storeParsedUrl = parse_url($storeUrl);

        $storeParsedQuery = array();
        if (isset($storeParsedUrl['query'])) {
            parse_str($storeParsedUrl['query'], $storeParsedQuery);
        }

        $currQuery = $this->_request->getQuery();
        if (isset($currQuery[$sidQueryParam]) && !empty($currQuery[$sidQueryParam])
            && $this->_getSession()->getSessionIdForHost($storeUrl) != $currQuery[$sidQueryParam]
        ) {
            unset($currQuery[$sidQueryParam]);
        }

        foreach ($currQuery as $k => $v) {
            $storeParsedQuery[$k] = $v;
        }

        if (!$this->isUseStoreInUrl()) {
            $storeParsedQuery['___store'] = $this->getCode();
        }
        if ($fromStore !== false) {
            $storeParsedQuery['___from_store'] = $fromStore === true ? Mage::app()->getStore()->getCode() : $fromStore;
        }

        return $storeParsedUrl['scheme'] . '://' . $storeParsedUrl['host']
            . (isset($storeParsedUrl['port']) ? ':' . $storeParsedUrl['port'] : '')
            . $storeParsedUrl['path'] . $requestString
            . ($storeParsedQuery ? '?'.http_build_query($storeParsedQuery, '', '&amp;') : '');
    }

    /**
     * Check if store is active
     *
     * @return boolean|null
     */
    public function getIsActive()
    {
        return $this->_getData('is_active');
    }

    /**
     * Retrieve store name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->_getData('name');
    }

    /**
     * Protect delete from non admin area
     *
     * Register indexing event before delete store
     *
     * @return Magento_Core_Model_Store
     */
    protected function _beforeDelete()
    {
        $this->_protectFromNonAdmin();
        Mage::getSingleton('Magento_Index_Model_Indexer')
            ->logEvent($this, self::ENTITY, Magento_Index_Model_Event::TYPE_DELETE);
        $this->_configDataResource->clearStoreData(array($this->getId()));
        return parent::_beforeDelete();
    }

    /**
     * rewrite in order to clear configuration cache
     *
     * @return Magento_Core_Model_Store
     */
    protected function _afterDelete()
    {
        parent::_afterDelete();
        $this->_configCacheType->clean();
        return $this;
    }

    /**
     * Init indexing process after store delete commit
     *
     * @return Magento_Core_Model_Store
     */
    protected function _afterDeleteCommit()
    {
        parent::_afterDeleteCommit();
        Mage::getSingleton('Magento_Index_Model_Indexer')->indexEvents(self::ENTITY, Magento_Index_Model_Event::TYPE_DELETE);
        return $this;
    }

    /**
     * Reinit and reset Config Data
     *
     * @return Magento_Core_Model_Store
     */
    public function resetConfig()
    {
        Mage::getConfig()->reinit();
        $this->_dirCache        = array();
        $this->_baseUrlCache    = array();
        $this->_urlCache        = array();

        return $this;
    }

    /**
     * Get/Set isReadOnly flag
     *
     * @param bool $value
     * @return bool
     */
    public function isReadOnly($value = null)
    {
        if (null !== $value) {
            $this->_isReadOnly = (bool) $value;
        }
        return $this->_isReadOnly;
    }

    /**
     * Retrieve storegroup name
     *
     * @return string
     */
    public function getFrontendName()
    {
        if (is_null($this->_frontendName)) {
            $storeGroupName = (string) Mage::getStoreConfig('general/store_information/name', $this);
            $this->_frontendName = (!empty($storeGroupName)) ? $storeGroupName : $this->getGroup()->getName();
        }
        return $this->_frontendName;
    }

    /**
     * Set url model for current store
     *
     * @param Magento_Core_Model_Url $urlModel
     * @return Magento_Core_Model_Store
     */
    public function setUrlModel($urlModel)
    {
        $this->_urlModel = $urlModel;
        return $this;
    }

    /**
     * Get url model by class name for current store
     *
     * @return Magento_Core_Model_Url
     */
    public function getUrlModel()
    {
        return $this->_urlModel;
    }
}
