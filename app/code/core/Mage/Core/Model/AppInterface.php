<?php
/**
 * Application interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

interface Mage_Core_Model_AppInterface
{
    /**
     * Initialize application without request processing
     *
     * @param  array $params
     * @return Mage_Core_Model_AppInterface
     */
    public function init(array $params);

    /**
     * Common logic for all run types
     *
     * @param  array $params
     * @return Mage_Core_Model_AppInterface
     */
    public function baseInit(array $params);

    /**
     * Run light version of application with specified modules support
     *
     * @see Mage_Core_Model_AppInterface->run()
     *
     * @param  array $params
     * @param  string|array $modules
     * @return Mage_Core_Model_AppInterface
     */
    public function initSpecified(array $params, $modules = array());

    /**
     * Run application. Run process responsible for request processing and sending response.
     *
     * @param array $params
     * @return Mage_Core_Model_AppInterface
     */
    public function run(array $params);

    /**
     * Get initialization parameter
     *
     * Returns false if key does not exist in array or the value is null
     *
     * @param string $key
     * @return mixed|bool
     */
    public function getInitParam($key);

    /**
     * Whether the application has been installed or not
     *
     * @return bool
     */
    public function isInstalled();

    /**
     * Throw an exception, if the application has not been installed yet
     *
     * @throws Magento_Exception
     */
    public function requireInstalledInstance();

    /**
     * Retrieve cookie object
     *
     * @return Mage_Core_Model_Cookie
     */
    public function getCookie();

    /**
     * Reinitialize stores
     *
     * @return void
     */
    public function reinitStores();

    /**
     * Check if system is run in the single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode();

    /**
     * Check if store has only one store view
     *
     * @return bool
     */
    public function hasSingleStore();

      /**
     * Set current default store
     *
     * @param string $store
     * @return Mage_Core_Model_AppInterface
     */
    public function setCurrentStore($store);

   /**
     * Re-declare custom error handler
     *
     * @param   string $handler
     * @return  Mage_Core_Model_AppInterface
     */
    public function setErrorHandler($handler);

    /**
     * Loading application area
     *
     * @param   string $code
     * @return  Mage_Core_Model_AppInterface
     */
    public function loadArea($code);

    /**
     * Loading part of area data
     *
     * @param   string $area
     * @param   string $part
     * @return  Mage_Core_Model_AppInterface
     */
    public function loadAreaPart($area, $part);

    /**
     * Retrieve application area
     *
     * @param   string $code
     * @return  Mage_Core_Model_App_Area
     */
    public function getArea($code);

    /**
     * Retrieve application store object
     *
     * @param null|string|bool|int|Mage_Core_Model_Store $id
     * @return Mage_Core_Model_Store
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getStore($id = null);

    /**
     * Retrieve application store object without Store_Exception
     *
     * @param string|int|Mage_Core_Model_Store $id
     * @return Mage_Core_Model_Store
     */
    public function getSafeStore($id = null);

    /**
     * Retrieve stores array
     *
     * @param bool $withDefault
     * @param bool $codeKey
     * @return array
     */
    public function getStores($withDefault = false, $codeKey = false);

    /**
     * Retrieve default store for default group and website
     *
     * @return Mage_Core_Model_Store
     */
    public function getDefaultStoreView();

    /**
     * Get distributive locale code
     *
     * @return string
     */
    public function getDistroLocaleCode();

    /**
     * Retrieve application website object
     *
     * @param null|bool|int|string|Mage_Core_Model_Website $id
     * @return Mage_Core_Model_Website
     * @throws Mage_Core_Exception
     */
    public function getWebsite($id = null);

    /**
     * Get websites
     *
     * @param bool $withDefault
     * @param bool $codeKey
     * @return array
     */
    public function getWebsites($withDefault = false, $codeKey = false);

    /**
     * Retrieve application store group object
     *
     * @param null|Mage_Core_Model_Store_Group|string $id
     * @return Mage_Core_Model_Store_Group
     * @throws Mage_Core_Exception
     */
    public function getGroup($id = null);

    /**
     * Retrieve application locale object
     *
     * @return Mage_Core_Model_Locale
     */
    public function getLocale();

    /**
     * Retrieve layout object
     *
     * @return Mage_Core_Model_Layout
     */
    public function getLayout();

    /**
     * Retrieve translate object
     *
     * @return Mage_Core_Model_Translate
     */
    public function getTranslator();

    /**
     * Retrieve helper object
     *
     * @param string $name
     * @return Mage_Core_Helper_Abstract
     */
    public function getHelper($name);

    /**
     * Retrieve application base currency code
     *
     * @return string
     */
    public function getBaseCurrencyCode();

    /**
     * Retrieve configuration object
     *
     * @return Mage_Core_Model_Config
     */
    public function getConfig();

    /**
     * Retrieve front controller object
     *
     * @return Mage_Core_Controller_Varien_Front
     */
    public function getFrontController();

    /**
     * Get core cache model
     *
     * @return Mage_Core_Model_Cache
     */
    public function getCacheInstance();


    /**
     * Retrieve cache object
     *
     * @return Zend_Cache_Core
     */
    public function getCache();

    /**
     * Loading cache data
     *
     * @param   string $id
     * @return  mixed
     */
    public function loadCache($id);

    /**
     * Saving cache data
     *
     * @param mixed $data
     * @param string $id
     * @param array $tags
     * @param bool $lifeTime
     * @return Mage_Core_Model_AppInterface
     */
    public function saveCache($data, $id, $tags = array(), $lifeTime = false);

    /**
     * Remove cache
     *
     * @param   string $id
     * @return  Mage_Core_Model_AppInterface
     */
    public function removeCache($id);

    /**
     * Cleaning cache
     *
     * @param   array $tags
     * @return  Mage_Core_Model_AppInterface
     */
    public function cleanCache($tags = array());

    /**
     * Check whether to use cache for specific component
     *
     * @param null|string $type
     * @return boolean
     */
    public function useCache($type = null);

    /**
     * Save cache usage settings
     *
     * @param array $data
     * @return Mage_Core_Model_AppInterface
     */
    public function saveUseCache($data);

    /**
     * Deletes all session files
     *
     * @return Mage_Core_Model_AppInterface
     */
    public function cleanAllSessions();

    /**
     * Retrieve request object
     *
     * @return Mage_Core_Controller_Request_Http
     */
    public function getRequest();

    /**
     * Request setter
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @return Mage_Core_Model_AppInterface
     */
    public function setRequest(Mage_Core_Controller_Request_Http $request);

    /**
     * Retrieve response object
     *
     * @return Zend_Controller_Response_Http
     */
    public function getResponse();

    /**
     * Response setter
     *
     * @param Mage_Core_Controller_Response_Http $response
     * @return Mage_Core_Model_AppInterface
     */
    public function setResponse(Mage_Core_Controller_Response_Http $response);

    /**
     * Add event area
     *
     * @param string $area
     * @return Mage_Core_Model_AppInterface
     */
    public function addEventArea($area);

    /**
     * Dispatch event
     *
     * @param string $eventName
     * @param mixed $args
     * @return Mage_Core_Model_AppInterface
     */
    public function dispatchEvent($eventName, $args);

    /**
     * @param $value
     * @return Mage_Core_Model_AppInterface
     */
    public function setUpdateMode($value);


    /**
     * @return mixed
     */
    public function getUpdateMode();

    /**
     * @throws Mage_Core_Model_Store_Exception
     */
    public function throwStoreException();

    /**
     * Set use session var instead of SID for URL
     *
     * @param bool $var
     * @return Mage_Core_Model_AppInterface
     */
    public function setUseSessionVar($var);

    /**
     * Retrieve use flag session var instead of SID for URL
     *
     * @return bool
     */
    public function getUseSessionVar();

    /**
     * Get either default or any store view
     *
     * @return Mage_Core_Model_Store
     */
    public function getAnyStoreView();

    /**
     * Set Use session in URL flag
     *
     * @param bool $flag
     * @return Mage_Core_Model_AppInterface
     */
    public function setUseSessionInUrl($flag = true);

    /**
     * Retrieve use session in URL flag
     *
     * @return bool
     */
    public function getUseSessionInUrl();

    /**
     * Allow or disallow single store mode
     *
     * @param bool $value
     * @return Mage_Core_Model_AppInterface
     */
    public function setIsSingleStoreModeAllowed($value);

    /**
     * Prepare array of store groups
     * can be filtered to contain default store group or not by $withDefault flag
     * depending on flag $codeKey array keys can be group id or group code
     *
     * @param bool $withDefault
     * @param bool $codeKey
     * @return array
     */
    public function getGroups($withDefault = false, $codeKey = false);

    /**
     * Get is cache locked
     *
     * @return bool
     */
    public function getIsCacheLocked();

    /**
     *  Unset website by id from app cache
     *
     * @param null|bool|int|string|Mage_Core_Model_Website $id
     */
    public function clearWebsiteCache($id = null);

    /**
     * Check if developer mode is enabled.
     *
     * @return bool
     */
    public function isDeveloperMode();
}
