<?php
/**
 * Application interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

interface Magento_Core_Model_AppInterface extends Magento_Core_Model_StoreManagerInterface
{
    /**
     * Default application locale
     */
    const DISTRO_LOCALE_CODE = 'en_US';

    /**
     * Default store Id (for install)
     */
    const DISTRO_STORE_ID       = 1;

    /**
     * Default store code (for install)
     *
     */
    const DISTRO_STORE_CODE     = Magento_Core_Model_Store::DEFAULT_CODE;

    /**
     * Admin store Id
     *
     */
    const ADMIN_STORE_ID = 0;

    /**
     * Dependency injection configuration node name
     */
    const CONFIGURATION_DI_NODE = 'di';

    /**
     * Run application. Run process responsible for request processing and sending response.
     *
     * @return Magento_Core_Model_AppInterface
     */
    public function run();

    /**
     * Throw an exception, if the application has not been installed yet
     *
     * @throws \Magento\Exception
     */
    public function requireInstalledInstance();

    /**
     * Retrieve cookie object
     *
     * @return Magento_Core_Model_Cookie
     */
    public function getCookie();

   /**
     * Re-declare custom error handler
     *
     * @param   string $handler
     * @return  Magento_Core_Model_AppInterface
     */
    public function setErrorHandler($handler);

    /**
     * Loading application area
     *
     * @param   string $code
     * @return  Magento_Core_Model_AppInterface
     */
    public function loadArea($code);

    /**
     * Loading part of area data
     *
     * @param   string $area
     * @param   string $part
     * @return  Magento_Core_Model_AppInterface
     */
    public function loadAreaPart($area, $part);

    /**
     * Retrieve application area
     *
     * @param   string $code
     * @return  Magento_Core_Model_App_Area
     */
    public function getArea($code);

    /**
     * Get distributive locale code
     *
     * @return string
     */
    public function getDistroLocaleCode();

    /**
     * Retrieve application locale object
     *
     * @return Magento_Core_Model_LocaleInterface
     */
    public function getLocale();

    /**
     * Retrieve layout object
     *
     * @return Magento_Core_Model_Layout
     */
    public function getLayout();

    /**
     * Retrieve helper object
     *
     * @param string $name
     * @return Magento_Core_Helper_Abstract
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
     * @return Magento_Core_Model_Config
     */
    public function getConfig();

    /**
     * Retrieve front controller object
     *
     * @return Magento_Core_Controller_Varien_Front
     */
    public function getFrontController();

    /**
     * Get core cache model
     *
     * @return Magento_Core_Model_CacheInterface
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
     * @param   string $cacheId
     * @return  mixed
     */
    public function loadCache($cacheId);

    /**
     * Saving cache data
     *
     * @param mixed $data
     * @param string $cacheId
     * @param array $tags
     * @param bool $lifeTime
     * @return Magento_Core_Model_AppInterface
     */
    public function saveCache($data, $cacheId, $tags = array(), $lifeTime = false);

    /**
     * Remove cache
     *
     * @param   string $cacheId
     * @return  Magento_Core_Model_AppInterface
     */
    public function removeCache($cacheId);

    /**
     * Cleaning cache
     *
     * @param   array $tags
     * @return  Magento_Core_Model_AppInterface
     */
    public function cleanCache($tags = array());

    /**
     * Deletes all session files
     *
     * @return Magento_Core_Model_AppInterface
     */
    public function cleanAllSessions();

    /**
     * Retrieve request object
     *
     * @return Magento_Core_Controller_Request_Http
     */
    public function getRequest();

    /**
     * Request setter
     *
     * @param Magento_Core_Controller_Request_Http $request
     * @return Magento_Core_Model_AppInterface
     */
    public function setRequest(Magento_Core_Controller_Request_Http $request);

    /**
     * Retrieve response object
     *
     * @return Zend_Controller_Response_Http
     */
    public function getResponse();

    /**
     * Response setter
     *
     * @param Magento_Core_Controller_Response_Http $response
     * @return Magento_Core_Model_AppInterface
     */
    public function setResponse(Magento_Core_Controller_Response_Http $response);

    /**
     * Set use session var instead of SID for URL
     *
     * @param bool $var
     * @return Magento_Core_Model_AppInterface
     */
    public function setUseSessionVar($var);

    /**
     * Retrieve use flag session var instead of SID for URL
     *
     * @return bool
     */
    public function getUseSessionVar();

    /**
     * Set Use session in URL flag
     *
     * @param bool $flag
     * @return Magento_Core_Model_AppInterface
     */
    public function setUseSessionInUrl($flag = true);

    /**
     * Retrieve use session in URL flag
     *
     * @return bool
     */
    public function getUseSessionInUrl();

    /**
     * Check if developer mode is enabled.
     *
     * @return bool
     */
    public function isDeveloperMode();
}
