<?php
/**
 * Application proxy model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_App_Proxy implements Magento_Core_Model_AppInterface
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Core_Model_App
     */
    protected $_app = null;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Get application model
     *
     * @return Magento_Core_Model_App
     */
    protected function _getApp()
    {
        if (null === $this->_app) {
            $this->_app = $this->_objectManager->get('Magento_Core_Model_App');
        }

        return $this->_app;
    }

    /**
     * Run application. Run process responsible for request processing and sending response.
     *
     * @return Magento_Core_Model_AppInterface
     */
    public function run()
    {
        return $this->_getApp()->run();
    }

    /**
     * Throw an exception, if the application has not been installed yet
     *
     * @throws Magento_Exception
     */
    public function requireInstalledInstance()
    {
        $this->_getApp()->requireInstalledInstance();
    }

    /**
     * Retrieve cookie object
     *
     * @return Magento_Core_Model_Cookie
     */
    public function getCookie()
    {
        return $this->_getApp()->getCookie();
    }

    /**
     * Reinitialize stores
     *
     * @return void
     */
    public function reinitStores()
    {
        $this->_getApp()->reinitStores();
    }

    /**
     * Check if system is run in the single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        return $this->_getApp()->isSingleStoreMode();
    }

    /**
     * Check if store has only one store view
     *
     * @return bool
     */
    public function hasSingleStore()
    {
        return $this->_getApp()->hasSingleStore();
    }

    /**
     * Set current default store
     *
     * @param string $store
     */
    public function setCurrentStore($store)
    {
        $this->_getApp()->setCurrentStore($store);
    }

    /**
     * Get current store code
     *
     * @return string
     */
    public function getCurrentStore()
    {
        return $this->_getApp()->getCurrentStore();
    }

    /**
     * Re-declare custom error handler
     *
     * @param   string $handler
     * @return  Magento_Core_Model_AppInterface
     */
    public function setErrorHandler($handler)
    {
        return $this->_getApp()->setErrorHandler($handler);
    }

    /**
     * Loading application area
     *
     * @param   string $code
     * @return  Magento_Core_Model_AppInterface
     */
    public function loadArea($code)
    {
        return $this->_getApp()->loadArea($code);
    }

    /**
     * Loading part of area data
     *
     * @param   string $area
     * @param   string $part
     * @return  Magento_Core_Model_AppInterface
     */
    public function loadAreaPart($area, $part)
    {
        return $this->_getApp()->loadAreaPart($area, $part);
    }

    /**
     * Retrieve application area
     *
     * @param   string $code
     * @return  Magento_Core_Model_App_Area
     */
    public function getArea($code)
    {
        return $this->_getApp()->getArea($code);
    }

    /**
     * Retrieve application store object
     *
     * @param null|string|bool|int|Magento_Core_Model_Store $storeId
     * @return Magento_Core_Model_Store
     * @throws Magento_Core_Model_Store_Exception
     */
    public function getStore($storeId = null)
    {
        return $this->_getApp()->getStore($storeId);
    }

    /**
     * Retrieve application store object without Store_Exception
     *
     * @param string|int|Magento_Core_Model_Store $storeId
     * @return Magento_Core_Model_Store
     */
    public function getSafeStore($storeId = null)
    {
        return $this->_getApp()->getSafeStore($storeId);
    }

    /**
     * Retrieve stores array
     *
     * @param bool $withDefault
     * @param bool $codeKey
     * @return array
     */
    public function getStores($withDefault = false, $codeKey = false)
    {
        return $this->_getApp()->getStores($withDefault, $codeKey);
    }

    /**
     * Retrieve default store for default group and website
     *
     * @return Magento_Core_Model_Store
     */
    public function getDefaultStoreView()
    {
        return $this->_getApp()->getDefaultStoreView();
    }

    /**
     * Get distributive locale code
     *
     * @return string
     */
    public function getDistroLocaleCode()
    {
        return $this->_getApp()->getDistroLocaleCode();
    }

    /**
     * Retrieve application website object
     *
     * @param null|bool|int|string|Magento_Core_Model_Website $websiteId
     * @return Magento_Core_Model_Website
     * @throws Magento_Core_Exception
     */
    public function getWebsite($websiteId = null)
    {
        return $this->_getApp()->getWebsite($websiteId);
    }

    /**
     * Get websites
     *
     * @param bool $withDefault
     * @param bool $codeKey
     * @return array
     */
    public function getWebsites($withDefault = false, $codeKey = false)
    {
        return $this->_getApp()->getWebsites($withDefault, $codeKey);
    }

    /**
     * Retrieve application store group object
     *
     * @param null|Magento_Core_Model_Store_Group|string $groupId
     * @return Magento_Core_Model_Store_Group
     * @throws Magento_Core_Exception
     */
    public function getGroup($groupId = null)
    {
        return $this->_getApp()->getGroup($groupId);
    }

    /**
     * Retrieve application locale object
     *
     * @return Magento_Core_Model_LocaleInterface
     */
    public function getLocale()
    {
        return $this->_getApp()->getLocale();
    }

    /**
     * Retrieve layout object
     *
     * @return Magento_Core_Model_Layout
     */
    public function getLayout()
    {
        return $this->_getApp()->getLayout();
    }

    /**
     * Retrieve helper object
     *
     * @param string $name
     * @return Magento_Core_Helper_Abstract
     */
    public function getHelper($name)
    {
        return $this->_getApp()->getHelper($name);
    }

    /**
     * Retrieve application base currency code
     *
     * @return string
     */
    public function getBaseCurrencyCode()
    {
        return $this->_getApp()->getBaseCurrencyCode();
    }

    /**
     * Retrieve configuration object
     *
     * @return Magento_Core_Model_Config
     */
    public function getConfig()
    {
        return $this->_getApp()->getConfig();
    }

    /**
     * Retrieve front controller object
     *
     * @return Magento_Core_Controller_Varien_Front
     */
    public function getFrontController()
    {
        return $this->_getApp()->getFrontController();
    }

    /**
     * Get core cache model
     *
     * @return Magento_Core_Model_CacheInterface
     */
    public function getCacheInstance()
    {
        return $this->_getApp()->getCacheInstance();
    }

    /**
     * Retrieve cache object
     *
     * @return Zend_Cache_Core
     */
    public function getCache()
    {
        return $this->_getApp()->getCache();
    }

    /**
     * Loading cache data
     *
     * @param   string $cacheId
     * @return  mixed
     */
    public function loadCache($cacheId)
    {
        return $this->_getApp()->loadCache($cacheId);
    }

    /**
     * Saving cache data
     *
     * @param mixed $data
     * @param string $cacheId
     * @param array $tags
     * @param bool $lifeTime
     * @return Magento_Core_Model_AppInterface
     */
    public function saveCache($data, $cacheId, $tags = array(), $lifeTime = false)
    {
        return $this->_getApp()->saveCache($data, $cacheId, $tags, $lifeTime);
    }

    /**
     * Remove cache
     *
     * @param   string $cacheId
     * @return  Magento_Core_Model_AppInterface
     */
    public function removeCache($cacheId)
    {
        return $this->_getApp()->removeCache($cacheId);
    }

    /**
     * Cleaning cache
     *
     * @param   array $tags
     * @return  Magento_Core_Model_AppInterface
     */
    public function cleanCache($tags = array())
    {
        return $this->_getApp()->cleanCache($tags);
    }

    /**
     * Deletes all session files
     *
     * @return Magento_Core_Model_AppInterface
     */
    public function cleanAllSessions()
    {
        return $this->_getApp()->cleanAllSessions();
    }

    /**
     * Retrieve request object
     *
     * @return Magento_Core_Controller_Request_Http
     */
    public function getRequest()
    {
        return $this->_getApp()->getRequest();
    }

    /**
     * Request setter
     *
     * @param Magento_Core_Controller_Request_Http $request
     * @return Magento_Core_Model_AppInterface
     */
    public function setRequest(Magento_Core_Controller_Request_Http $request)
    {
        return $this->_getApp()->setRequest($request);
    }

    /**
     * Retrieve response object
     *
     * @return Zend_Controller_Response_Http
     */
    public function getResponse()
    {
        return $this->_getApp()->getResponse();
    }

    /**
     * Response setter
     *
     * @param Magento_Core_Controller_Response_Http $response
     * @return Magento_Core_Model_AppInterface
     */
    public function setResponse(Magento_Core_Controller_Response_Http $response)
    {
        return $this->_getApp()->setResponse($response);
    }

   /**
     * @throws Magento_Core_Model_Store_Exception
     */
    public function throwStoreException()
    {
        $this->_getApp()->throwStoreException();
    }

    /**
     * Set use session var instead of SID for URL
     *
     * @param bool $var
     * @return Magento_Core_Model_AppInterface
     */
    public function setUseSessionVar($var)
    {
        return $this->_getApp()->setUseSessionVar($var);
    }

    /**
     * Retrieve use flag session var instead of SID for URL
     *
     * @return bool
     */
    public function getUseSessionVar()
    {
        return $this->_getApp()->getUseSessionVar();
    }

    /**
     * Get either default or any store view
     *
     * @return Magento_Core_Model_Store
     */
    public function getAnyStoreView()
    {
        return $this->_getApp()->getAnyStoreView();
    }

    /**
     * Set Use session in URL flag
     *
     * @param bool $flag
     * @return Magento_Core_Model_AppInterface
     */
    public function setUseSessionInUrl($flag = true)
    {
        return $this->_getApp()->setUseSessionInUrl($flag);
    }

    /**
     * Retrieve use session in URL flag
     *
     * @return bool
     */
    public function getUseSessionInUrl()
    {
        return $this->_getApp()->getUseSessionInUrl();
    }

    /**
     * Allow or disallow single store mode
     *
     * @param bool $value
     */
    public function setIsSingleStoreModeAllowed($value)
    {
        $this->_getApp()->setIsSingleStoreModeAllowed($value);
    }

    /**
     * Prepare array of store groups
     * can be filtered to contain default store group or not by $withDefault flag
     * depending on flag $codeKey array keys can be group id or group code
     *
     * @param bool $withDefault
     * @param bool $codeKey
     * @return array
     */
    public function getGroups($withDefault = false, $codeKey = false)
    {
        return $this->_getApp()->getGroups($withDefault, $codeKey);
    }

    /**
     *  Unset website by id from app cache
     *
     * @param null|bool|int|string|Magento_Core_Model_Website $websiteId
     */
    public function clearWebsiteCache($websiteId = null)
    {
        $this->_getApp()->clearWebsiteCache($websiteId);
    }

    /**
     * Check if developer mode is enabled.
     *
     * @return bool
     */
    public function isDeveloperMode()
    {
        return $this->_getApp()->isDeveloperMode();
    }
}
