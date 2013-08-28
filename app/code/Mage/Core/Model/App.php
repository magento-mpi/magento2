<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Application model
 *
 * Application should have: areas, store, locale, translator, design package
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_App implements Mage_Core_Model_AppInterface
{
    /**
     * Application loaded areas array
     *
     * @var array
     */
    protected $_areas = array();

    /**
     * Application location object
     *
     * @var Mage_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * Application configuration object
     *
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * Application front controller
     *
     * @var Mage_Core_Controller_FrontInterface
     */
    protected $_frontController;

    /**
     * Flag to identify whether front controller is initialized
     *
     * @var bool
     */
    protected $_isFrontControllerInitialized = false;

    /**
     * Cache object
     *
     * @var Mage_Core_Model_CacheInterface
     */
    protected $_cache;

    /**
     * Request object
     *
     * @var Zend_Controller_Request_Http
     */
    protected $_request;

    /**
     * Response object
     *
     * @var Zend_Controller_Response_Http
     */
    protected $_response;

    /**
     * Use session in URL flag
     *
     * @see Mage_Core_Model_Url
     * @var bool
     */
    protected $_useSessionInUrl = true;

    /**
     * Use session var instead of SID for session in URL
     *
     * @var bool
     */
    protected $_useSessionVar = false;

    /**
     * Object manager
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Data base updater object
     *
     * @var Mage_Core_Model_Db_UpdaterInterface
     */
    protected $_dbUpdater;

    /**
     * Store list manager
     *
     * @var Mage_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Mage_Core_Model_App_State
     */
    protected $_appState;

    /**
     * @var Mage_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * @var Mage_Core_Model_Config_Scope
     */
    protected $_configScope;

    /**
     * @param Mage_Core_Model_Config $config
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_CacheInterface $cache
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Model_Db_UpdaterInterface $dbUpdater
     * @param Mage_Core_Model_StoreManagerInterface $storeManager
     * @param Mage_Core_Model_Event_Manager $eventManager
     * @param Mage_Core_Model_App_State $appState
     * @param Mage_Core_Model_Config_Scope $configScope
     */
    public function __construct(
        Mage_Core_Model_Config $config,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_CacheInterface $cache,
        Magento_ObjectManager $objectManager,
        Mage_Core_Model_Db_UpdaterInterface $dbUpdater,
        Mage_Core_Model_StoreManagerInterface $storeManager,
        Mage_Core_Model_Event_Manager $eventManager,
        Mage_Core_Model_App_State $appState,
        Mage_Core_Model_Config_Scope $configScope
    ) {
        $this->_config = $config;
        $this->_cache = $cache;
        $this->_objectManager = $objectManager;
        $this->_storeManager = $storeManager;
        $this->_dbUpdater = $dbUpdater;
        $this->_frontController = $frontController;
        $this->_appState = $appState;
        $this->_eventManager = $eventManager;
        $this->_configScope = $configScope;
    }

    /**
     * Run application. Run process responsible for request processing and sending response.
     *
     * @return Mage_Core_Model_App
     */
    public function run()
    {
        Magento_Profiler::start('init');

        if ($this->_appState->isInstalled() && !$this->_cache->load('data_upgrade')) {
            $this->_dbUpdater->updateScheme();
            $this->_dbUpdater->updateData();
            $this->_cache->save(1, 'data_upgrade');
        }
        $this->_initRequest();

        $controllerFront = $this->getFrontController();
        Magento_Profiler::stop('init');

        $controllerFront->dispatch();

        return $this;
    }

    /**
     * Throw an exception, if the application has not been installed yet
     *
     * @throws Magento_Exception
     */
    public function requireInstalledInstance()
    {
        if (false == $this->_appState->isInstalled()) {
            throw new Magento_Exception('Application is not installed yet, please complete the installation first.');
        }
    }

    /**
     * Init request object
     *
     * @return Mage_Core_Model_App
     */
    protected function _initRequest()
    {
        $this->getRequest()->setPathInfo();
        return $this;
    }

    /**
     * Retrieve cookie object
     *
     * @return Mage_Core_Model_Cookie
     */
    public function getCookie()
    {
        return $this->_objectManager->get('Mage_Core_Model_Cookie');
    }

    /**
     * Initialize application front controller
     *
     * @return Mage_Core_Model_App
     */
    protected function _initFrontController()
    {
        $this->_frontController = $this->_getFrontControllerByCurrentArea();
        return $this;
    }

    /**
     * Instantiate proper front controller instance depending on current area
     *
     * @return Mage_Core_Controller_FrontInterface
     */
    protected function _getFrontControllerByCurrentArea()
    {
        /**
         * TODO: Temporary implementation for API. Must be reconsidered during implementation
         * TODO: of ability to set different front controllers in different area.
         * TODO: See also related changes in Mage_Core_Model_Config.
         */
        // TODO: Assure that everything work fine work in areas without routers (e.g. URL generation)
        /** Default front controller class */
        $frontControllerClass = 'Mage_Core_Controller_Varien_Front';
        $pathParts = explode('/', trim($this->getRequest()->getPathInfo(), '/'));
        if ($pathParts) {
            /** If area front name is used it is expected to be set on the first place in path info */
            $frontName = reset($pathParts);
            foreach ($this->getConfig()->getAreas() as $areaCode => $areaInfo) {
                if (isset($areaInfo['front_controller'])
                    && isset($areaInfo['frontName']) && ($frontName == $areaInfo['frontName'])
                ) {
                    $this->_configScope->setCurrentScope($areaCode);
                    $frontControllerClass = $areaInfo['front_controller'];
                    break;
                }
            }
        }
        return $this->_objectManager->get($frontControllerClass);
    }

    /**
     * Re-declare custom error handler
     *
     * @param   string $handler
     * @return  Mage_Core_Model_App
     */
    public function setErrorHandler($handler)
    {
        set_error_handler($handler);
        return $this;
    }

    /**
     * Loading application area
     *
     * @param   string $code
     * @return  Mage_Core_Model_App
     */
    public function loadArea($code)
    {
        $this->_configScope->setCurrentScope($code);
        $this->getArea($code)->load();
        return $this;
    }

    /**
     * Loading part of area data
     *
     * @param   string $area
     * @param   string $part
     * @return  Mage_Core_Model_App
     */
    public function loadAreaPart($area, $part)
    {
        $this->getArea($area)->load($part);
        return $this;
    }

    /**
     * Retrieve application area
     *
     * @param   string $code
     * @return  Mage_Core_Model_App_Area
     */
    public function getArea($code)
    {
        if (!isset($this->_areas[$code])) {
            $this->_areas[$code] = $this->_objectManager->create(
                'Mage_Core_Model_App_Area',
                array('areaCode' => $code)
            );
        }
        return $this->_areas[$code];
    }

    /**
     * Get distro locale code
     *
     * @return string
     */
    public function getDistroLocaleCode()
    {
        return self::DISTRO_LOCALE_CODE;
    }

    /**
     * Retrieve application locale object
     *
     * @return Mage_Core_Model_LocaleInterface
     */
    public function getLocale()
    {
        if (!$this->_locale) {
            $this->_locale = $this->_objectManager->get('Mage_Core_Model_LocaleInterface');
        }
        return $this->_locale;
    }

    /**
     * Retrieve layout object
     *
     * @return Mage_Core_Model_Layout
     */
    public function getLayout()
    {
        return $this->_objectManager->get('Mage_Core_Model_Layout');
    }

    /**
     * Retrieve helper object
     *
     * @param string $name
     * @return Mage_Core_Helper_Abstract
     */
    public function getHelper($name)
    {
        return Mage::helper($name);
    }

    /**
     * Retrieve application base currency code
     *
     * @return string
     */
    public function getBaseCurrencyCode()
    {
        return $this->_config->getValue(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE, 'default');
    }

    /**
     * Retrieve configuration object
     *
     * @return Mage_Core_Model_Config
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Retrieve front controller object
     *
     * @return Mage_Core_Controller_Varien_Front
     */
    public function getFrontController()
    {
        if (!$this->_isFrontControllerInitialized) {
            $this->_initFrontController();
            $this->_isFrontControllerInitialized = true;
        }
        return $this->_frontController;
    }

    /**
     * Get core cache model
     *
     * @return Mage_Core_Model_CacheInterface
     */
    public function getCacheInstance()
    {
        return $this->_cache;
    }

    /**
     * Retrieve cache object
     *
     * @return Magento_Cache_FrontendInterface
     */
    public function getCache()
    {
        return $this->_cache->getFrontend();
    }

    /**
     * Loading cache data
     *
     * @param   string $cacheId
     * @return  mixed
     */
    public function loadCache($cacheId)
    {
        return $this->_cache->load($cacheId);
    }

    /**
     * Saving cache data
     *
     * @param mixed $data
     * @param string $cacheId
     * @param array $tags
     * @param bool $lifeTime
     * @return Mage_Core_Model_App
     */
    public function saveCache($data, $cacheId, $tags = array(), $lifeTime = false)
    {
        $this->_cache->save($data, $cacheId, $tags, $lifeTime);
        return $this;
    }

    /**
     * Remove cache
     *
     * @param   string $cacheId
     * @return  Mage_Core_Model_App
     */
    public function removeCache($cacheId)
    {
        $this->_cache->remove($cacheId);
        return $this;
    }

    /**
     * Cleaning cache
     *
     * @param   array $tags
     * @return  Mage_Core_Model_App
     */
    public function cleanCache($tags = array())
    {
        $this->_cache->clean($tags);
        $this->_eventManager->dispatch('application_clean_cache', array('tags' => $tags));
        return $this;
    }

    /**
     * Deletes all session files
     *
     * @return Mage_Core_Model_App
     */
    public function cleanAllSessions()
    {
        if (session_module_name() == 'files') {
            /** @var Magento_Filesystem $filesystem */
            $filesystem = $this->_objectManager->create('Magento_Filesystem');
            $filesystem->delete(session_save_path());
        }
        return $this;
    }

    /**
     * Retrieve request object
     *
     * @return Mage_Core_Controller_Request_Http
     */
    public function getRequest()
    {
        if (!$this->_request) {
            $this->_request = $this->_objectManager->get('Mage_Core_Controller_Request_Http');
        }
        return $this->_request;
    }

    /**
     * Request setter
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @return Mage_Core_Model_App
     */
    public function setRequest(Mage_Core_Controller_Request_Http $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * Retrieve response object
     *
     * @return Zend_Controller_Response_Http
     */
    public function getResponse()
    {
        if (!$this->_response) {
            $this->_response = $this->_objectManager->get('Mage_Core_Controller_Response_Http');
            $this->_response->headersSentThrowsException = Mage::$headersSentThrowsException;
            $this->_response->setHeader('Content-Type', 'text/html; charset=UTF-8');
        }
        return $this->_response;
    }

    /**
     * Response setter
     *
     * @param Mage_Core_Controller_Response_Http $response
     * @return Mage_Core_Model_App
     */
    public function setResponse(Mage_Core_Controller_Response_Http $response)
    {
        $this->_response = $response;
        return $this;
    }

    /**
     * Set use session var instead of SID for URL
     *
     * @param bool $var
     * @return Mage_Core_Model_App
     */
    public function setUseSessionVar($var)
    {
        $this->_useSessionVar = (bool)$var;
        return $this;
    }

    /**
     * Retrieve use flag session var instead of SID for URL
     *
     * @return bool
     */
    public function getUseSessionVar()
    {
        return $this->_useSessionVar;
    }

    /**
     * Set Use session in URL flag
     *
     * @param bool $flag
     * @return Mage_Core_Model_App
     */
    public function setUseSessionInUrl($flag = true)
    {
        $this->_useSessionInUrl = (bool)$flag;
        return $this;
    }

    /**
     * Retrieve use session in URL flag
     *
     * @return bool
     */
    public function getUseSessionInUrl()
    {
        return $this->_useSessionInUrl;
    }

    /**
     * Check if developer mode is enabled
     *
     * @return bool
     */
    public function isDeveloperMode()
    {
        return $this->_appState->getMode() == Mage_Core_Model_App_State::MODE_DEVELOPER;
    }

    /**
     * Retrieve application store object without Store_Exception
     *
     * @param string|int|Mage_Core_Model_Store $storeId
     * @return Mage_Core_Model_Store
     *
     * @deprecated use Mage_Core_Model_StoreManagerInterface::getSafeStore()
     */
    public function getSafeStore($storeId = null)
    {
        return $this->_storeManager->getSafeStore($storeId);
    }

    /**
     * Allow or disallow single store mode
     *
     * @param bool $value
     *
     * @deprecated use Mage_Core_Model_StoreManager::setIsSingleStoreModeAllowed()
     */
    public function setIsSingleStoreModeAllowed($value)
    {
        $this->_storeManager->setIsSingleStoreModeAllowed($value);
    }

    /**
     * Check if store has only one store view
     *
     * @return bool
     *
     * @deprecated use Mage_Core_Model_StoreManager::hasSingleStore()
     */
    public function hasSingleStore()
    {
        return $this->_storeManager->hasSingleStore();
    }

    /**
     * Check if system is run in the single store mode
     *
     * @return bool
     *
     * @deprecated use Mage_Core_Model_StoreManager::isSingleStoreMode()
     */
    public function isSingleStoreMode()
    {
        return $this->_storeManager->isSingleStoreMode();
    }

    /**
     * @throws Mage_Core_Model_Store_Exception
     *
     * @deprecated use Mage_Core_Model_StoreManager::throwStoreException()
     */
    public function throwStoreException()
    {
        $this->_storeManager->throwStoreException();
    }

    /**
     * Retrieve application store object
     *
     * @param null|string|bool|int|Mage_Core_Model_Store $storeId
     * @return Mage_Core_Model_Store
     * @throws Mage_Core_Model_Store_Exception
     *
     * @deprecated use Mage_Core_Model_StoreManager::getStore()
     */
    public function getStore($storeId = null)
    {
        return $this->_storeManager->getStore($storeId);
    }

    /**
     * Retrieve stores array
     *
     * @param bool $withDefault
     * @param bool $codeKey
     * @return Mage_Core_Model_Store[]
     *
     * @deprecated use Mage_Core_Model_StoreManager::getStores()
     */
    public function getStores($withDefault = false, $codeKey = false)
    {
        return $this->_storeManager->getStores($withDefault, $codeKey);
    }

    /**
     * Retrieve application website object
     *
     * @param null|bool|int|string|Mage_Core_Model_Website $websiteId
     * @return Mage_Core_Model_Website
     * @throws Mage_Core_Exception
     *
     * @deprecated use Mage_Core_Model_StoreManager::getWebsite()
     */
    public function getWebsite($websiteId = null)
    {
        return $this->_storeManager->getWebsite($websiteId);
    }

    /**
     * Get loaded websites
     *
     * @param bool $withDefault
     * @param bool|string $codeKey
     * @return Mage_Core_Model_Website[]
     *
     * @deprecated use Mage_Core_Model_StoreManager::getWebsites()
     */
    public function getWebsites($withDefault = false, $codeKey = false)
    {
        return $this->_storeManager->getWebsites($withDefault, $codeKey);
    }

    /**
     * Reinitialize store list
     *
     * @deprecated use Mage_Core_Model_StoreManager::reinitStores()
     */
    public function reinitStores()
    {
        $this->_storeManager->reinitStores();
    }

    /**
     * Set current default store
     *
     * @param string $store
     *
     * @deprecated use Mage_Core_Model_StoreManager::setCurrentStore()
     */
    public function setCurrentStore($store)
    {
        $this->_storeManager->setCurrentStore($store);
    }

    /**
     * Get current store code
     *
     * @return string
     *
     * @deprecated use Mage_Core_Model_StoreManager::getCurrentStore()
     */
    public function getCurrentStore()
    {
        return $this->_storeManager->getCurrentStore();
    }


    /**
     * Retrieve default store for default group and website
     *
     * @return Mage_Core_Model_Store
     *
     * @deprecated use Mage_Core_Model_StoreManager::getDefaultStoreView()
     */
    public function getDefaultStoreView()
    {
        return $this->_storeManager->getDefaultStoreView();
    }

    /**
     * Retrieve application store group object
     *
     * @param null|Mage_Core_Model_Store_Group|string $groupId
     * @return Mage_Core_Model_Store_Group
     * @throws Mage_Core_Exception
     *
     * @deprecated use Mage_Core_Model_StoreManager::getGroup()
     */
    public function getGroup($groupId = null)
    {
        return $this->_storeManager->getGroup($groupId);
    }

    /**
     * Prepare array of store groups
     * can be filtered to contain default store group or not by $withDefault flag
     * depending on flag $codeKey array keys can be group id or group code
     *
     * @param bool $withDefault
     * @param bool $codeKey
     * @return Mage_Core_Model_Store_Group[]
     *
     * @deprecated use Mage_Core_Model_StoreManager::getGroups()
     */
    public function getGroups($withDefault = false, $codeKey = false)
    {
        return $this->_storeManager->getGroups($withDefault, $codeKey);
    }

    /**
     *  Unset website by id from app cache
     *
     * @param null|bool|int|string|Mage_Core_Model_Website $websiteId
     *
     * @deprecated use Mage_Core_Model_StoreManager::clearWebsiteCache()
     */
    public function clearWebsiteCache($websiteId = null)
    {
        $this->_storeManager->clearWebsiteCache($websiteId);
    }

    /**
     * Get either default or any store view
     *
     * @return Mage_Core_Model_Store|null
     *
     * @deprecated use Mage_Core_Model_StoreManager::getAnyStoreView()
     */
    public function getAnyStoreView()
    {
        return $this->_storeManager->getAnyStoreView();
    }
}
