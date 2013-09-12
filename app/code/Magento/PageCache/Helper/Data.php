<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Page cache data helper
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_PageCache_Helper_Data extends Magento_Core_Helper_Abstract
{
    /**
     * Paths to external cache config options
     */
    const XML_PATH_EXTERNAL_CACHE_ENABLED  = 'system/external_page_cache/enabled';
    const XML_PATH_EXTERNAL_CACHE_LIFETIME = 'system/external_page_cache/cookie_lifetime';
    const XML_PATH_EXTERNAL_CACHE_CONTROL  = 'system/external_page_cache/control';

    /**
     * Path to external cache controls
     */
    const XML_PATH_EXTERNAL_CACHE_CONTROLS = 'global/external_cache/controls';

    /**
     * Cookie name for disabling external caching
     */
    const NO_CACHE_COOKIE = 'external_no_cache';

    /**
     * Cookie name for locking the NO_CACHE_COOKIE for modification
     */
    const NO_CACHE_LOCK_COOKIE = 'external_no_cache_cookie_locked';

    /**
     * @var bool
     */
    protected $_isNoCacheCookieLocked = false;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * Initialize 'no cache' cookie locking
     *
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     */
    function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Store_Config $coreStoreConfig
    ) {
        parent::__construct($context);
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_isNoCacheCookieLocked = (bool)$this->_getCookie()->get(self::NO_CACHE_LOCK_COOKIE);
    }

    /**
     * Retrieve the cookie model instance
     *
     * @return Magento_Core_Model_Cookie
     */
    protected function _getCookie()
    {
        return Mage::getSingleton('Magento_Core_Model_Cookie');
    }

    /**
     * Check whether external cache is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)$this->_coreStoreConfig->getConfig(self::XML_PATH_EXTERNAL_CACHE_ENABLED);
    }

    /**
     * Return all available external cache controls
     *
     * @return array
     */
    public function getCacheControls()
    {
        $controls = Mage::app()->getConfig()->getNode(self::XML_PATH_EXTERNAL_CACHE_CONTROLS);
        return $controls->asCanonicalArray();
    }

    /**
     * Initialize proper external cache control model
     *
     * @throws Magento_Core_Exception
     * @return Magento_PageCache_Model_Control_Interface
     */
    public function getCacheControlInstance()
    {
        $usedControl = $this->_coreStoreConfig->getConfig(self::XML_PATH_EXTERNAL_CACHE_CONTROL);
        if ($usedControl) {
            foreach ($this->getCacheControls() as $control => $info) {
                if ($control == $usedControl && !empty($info['class'])) {
                    return Mage::getSingleton($info['class']);
                }
            }
        }
        Mage::throwException(__('Failed to load external cache control'));
    }

    /**
     * Disable caching on external storage side by setting special cookie, if the cookie has not been locked
     *
     * @param int|null $lifetime
     * @return Magento_PageCache_Helper_Data
     */
    public function setNoCacheCookie($lifetime = null)
    {
        if ($this->_isNoCacheCookieLocked) {
            return $this;
        }
        $lifetime = $lifetime !== null ? $lifetime : $this->_coreStoreConfig->getConfig(self::XML_PATH_EXTERNAL_CACHE_LIFETIME);
        if ($this->_getCookie()->get(self::NO_CACHE_COOKIE)) {
            $this->_getCookie()->renew(self::NO_CACHE_COOKIE, $lifetime);
        } else {
            $this->_getCookie()->set(self::NO_CACHE_COOKIE, '1', $lifetime);
        }
        return $this;
    }

    /**
     * Remove the 'no cache' cookie, if it has not been locked
     *
     * @return Magento_PageCache_Helper_Data
     */
    public function removeNoCacheCookie()
    {
        if (!$this->_isNoCacheCookieLocked) {
            $this->_getCookie()->delete(self::NO_CACHE_COOKIE);
        }
        return $this;
    }

    /**
     * Disable modification of the 'no cache' cookie
     *
     * @return Magento_PageCache_Helper_Data
     */
    public function lockNoCacheCookie()
    {
        $this->_getCookie()->set(self::NO_CACHE_LOCK_COOKIE, '1', 0);
        $this->_isNoCacheCookieLocked = true;
        return $this;
    }

    /**
     * Enable modification of the 'no cache' cookie
     *
     * @return Magento_PageCache_Helper_Data
     */
    public function unlockNoCacheCookie()
    {
        $this->_getCookie()->delete(self::NO_CACHE_LOCK_COOKIE);
        $this->_isNoCacheCookieLocked = false;
        return $this;
    }

    /**
     * Returns a lifetime of cookie for external cache
     *
     * @return string Time in seconds
     */
    public function getNoCacheCookieLifetime()
    {
        return $this->_coreStoreConfig->getConfig(self::XML_PATH_EXTERNAL_CACHE_LIFETIME);
    }
}
