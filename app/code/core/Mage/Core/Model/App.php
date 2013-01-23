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
class Mage_Core_Model_App
{
    /**
     * Application initialization option to inject custom request object
     */
    const INIT_OPTION_REQUEST  = 'request';

    /**
     * Application initialization option to inject custom response object
     */
    const INIT_OPTION_RESPONSE = 'response';

    /**#@+
     * Available scope types
     */
    const SCOPE_TYPE_STORE   = 'store';
    const SCOPE_TYPE_GROUP   = 'group';
    const SCOPE_TYPE_WEBSITE = 'website';
    /**#@-*/

    const DEFAULT_ERROR_HANDLER = 'mageCoreErrorHandler';

    /**
     * Default application locale
     */
    const DISTRO_LOCALE_CODE = 'en_US';

    /**
     * Cache tag for all cache data exclude config cache
     *
     */
    const CACHE_TAG = 'MAGE';

    /**
     * Default store Id (for install)
     */
    const DISTRO_STORE_ID       = 1;

    /**
     * Default store code (for install)
     *
     */
    const DISTRO_STORE_CODE     = 'default';

    /**
     * Admin store Id
     *
     */
    const ADMIN_STORE_ID = 0;

    /**
     * Application loaded areas array
     *
     * @var array
     */
    protected $_areas = array();

    /**
     * Application store object
     *
     * @var Mage_Core_Model_Store
     */
    protected $_store;

    /**
     * Application website object
     *
     * @var Mage_Core_Model_Website
     */
    protected $_website;

    /**
     * Application location object
     *
     * @var Mage_Core_Model_Locale
     */
    protected $_locale;

    /**
     * Application translate object
     *
     * @var Mage_Core_Model_Translate
     */
    protected $_translator;

    /**
     * Application design package object
     *
     * @var Mage_Core_Model_Design_Package
     */
    protected $_design;

    /**
     * Application layout object
     *
     * @var Mage_Core_Model_Layout
     */
    protected $_layout;

    /**
     * Initialization parameters
     *
     * @var array
     */
    protected $_initParams = null;

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
     * @var Mage_Core_Model_Cache
     */
    protected $_cache;

    /**
     * Use Cache
     *
     * @var array
     */
    protected $_useCache;

    /**
     * Websites cache
     *
     * @var array
     */
    protected $_websites = array();

    /**
     * Groups cache
     *
     * @var array
     */
    protected $_groups = array();

    /**
     * Stores cache
     *
     * @var array
     */
    protected $_stores = array();

    /**
     * Flag that shows that system has only one store view
     *
     * @var bool
     */
    protected $_hasSingleStore;

    /**
     * @var bool
     */
    protected $_isSingleStoreAllowed = true;

    /**
     * Default store code
     *
     * @var string
     */
    protected $_currentStore = null;

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
     * Update process run flag
     *
     * @var bool
     */
    protected $_updateMode = false;

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
     * Cache locked flag
     *
     * @var null|bool
     */
    protected $_isCacheLocked = null;

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
     * Flag that checks whether store was initialized
     * @var bool
     */
    protected $_isStoreInitialized;

    /**
     * @param Mage_Core_Model_Config $config
     * @param Mage_Core_Model_Logger $log
     * @param Mage_Core_Model_Cache $cache
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Model_Db_UpdaterInterface $dbUpdater
     * @param $scopeCode
     * @param $scopeType
     */
    public function __construct(
        Mage_Core_Model_Config $config,
        Mage_Core_Model_Logger $log,
        Mage_Core_Model_Cache $cache,
        Magento_ObjectManager $objectManager,
        Mage_Core_Model_Db_UpdaterInterface $dbUpdater,
        $scopeCode,
        $scopeType
    ) {
        $this->_config = $config;
        $this->_cache = $cache;
        $this->_log = $log;
        $this->_objectManager = $objectManager;
        $this->_initEnvironment();
        $this->_dbUpdater = $dbUpdater;
        $this->_scopeCode = $scopeCode;
        $this->_scopeType = $scopeType;
    }

    /**
     * Initialize application store
     */
    protected function _initializeStore()
    {
        if (!$this->_isStoreInitialized && Mage::isInstalled()) {
            $this->_isStoreInitialized = true;
            $currentStore = $this->_currentStore;
            $this->_initCurrentStore($this->_scopeCode, $this->_scopeType ?: self::SCOPE_TYPE_STORE);
            if ($currentStore) {
                $this->_currentStore = $currentStore;
            }
            $this->_log->initForStore($this->_store, $this->_config);
        }
    }

    /**
     * Initialize application without request processing
     *
     * @param  array $params
     * @return Mage_Core_Model_App
     */
    public function init()
    {
        Magento_Profiler::start('self::app::init');
        Magento_Profiler::start('init_config');
        Magento_Profiler::stop('init_config');

        $this->_initializeStore();
        if (Mage::isInstalled()) {
            $this->_initRequest();
        }
        Magento_Profiler::stop('self::app::init');
        return $this;
    }

    /**
     * Run application. Run process responsible for request processing and sending response.
     *
     * @return Mage_Core_Model_App
     */
    public function run()
    {
        Magento_Profiler::start('init');

        $this->_initializeStore();
        $this->_initRequest();
        $this->_dbUpdater->updateData();

        $controllerFront = $this->getFrontController();
        Magento_Profiler::stop('init');

        $controllerFront->dispatch();

        return $this;
    }

    /**
     * Get initialization parameter
     *
     * Returns false if key does not exist in array or the value is null
     *
     * @param string $key
     * @return mixed|bool
     */
    public function getInitParam($key)
    {
        return isset($this->_initParams[$key]) ? $this->_initParams[$key] : false;
    }

    /**
     * Throw an exception, if the application has not been installed yet
     *
     * @throws Magento_Exception
     */
    public function requireInstalledInstance()
    {
        if (!Mage::isInstalled()) {
            throw new Magento_Exception('Application is not installed yet, please complete the installation first.');
        }
    }

    /**
     * Initialize PHP environment
     *
     * @return Mage_Core_Model_App
     */
    protected function _initEnvironment()
    {
        $this->setErrorHandler(self::DEFAULT_ERROR_HANDLER);
        date_default_timezone_set(Mage_Core_Model_Locale::DEFAULT_TIMEZONE);
        return $this;
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
     * Initialize currently ran store
     *
     * @param string $scopeCode code of default scope (website/store_group/store code)
     * @param string $scopeType type of default scope (website/group/store)
     * @return Mage_Core_Model_App
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _initCurrentStore($scopeCode, $scopeType)
    {
        Magento_Profiler::start('init_stores');
        $this->_initStores();
        Magento_Profiler::stop('init_stores');

        if (empty($scopeCode) && !is_null($this->_website)) {
            $scopeCode = $this->_website->getCode();
            $scopeType = self::SCOPE_TYPE_WEBSITE;
        }
        switch ($scopeType) {
            case self::SCOPE_TYPE_STORE:
                $this->_currentStore = $scopeCode;
                break;
            case self::SCOPE_TYPE_GROUP:
                $this->_currentStore = $this->_getStoreByGroup($scopeCode);
                break;
            case self::SCOPE_TYPE_WEBSITE:
                $this->_currentStore = $this->_getStoreByWebsite($scopeCode);
                break;
            default:
                $this->throwStoreException();
        }

        if (!empty($this->_currentStore)) {
            $this->_checkCookieStore($scopeType);
            $this->_checkGetStore($scopeType);
        }
        $this->_useSessionInUrl = $this->getStore()->getConfig(
            Mage_Core_Model_Session_Abstract::XML_PATH_USE_FRONTEND_SID
        );
        Mage::dispatchEvent('core_app_init_current_store_after');
        return $this;
    }

    /**
     * Retrieve cookie object
     *
     * @return Mage_Core_Model_Cookie
     */
    public function getCookie()
    {
        return Mage::getSingleton('Mage_Core_Model_Cookie');
    }

    /**
     * Check get store
     *
     * @param string $type
     * @return Mage_Core_Model_App
     */
    protected function _checkGetStore($type)
    {
        if (empty($_GET)) {
            return $this;
        }

        /**
         * @todo check XML_PATH_STORE_IN_URL
         */
        if (!isset($_GET['___store'])) {
            return $this;
        }

        $store = $_GET['___store'];
        if (!isset($this->_stores[$store])) {
            return $this;
        }

        $storeObj = $this->_stores[$store];
        if (!$storeObj->getId() || !$storeObj->getIsActive()) {
            return $this;
        }

        /**
         * prevent running a store from another website or store group,
         * if website or store group was specified explicitly
         */
        $curStoreObj = $this->_stores[$this->_currentStore];
        if ($type == 'website' && $storeObj->getWebsiteId() == $curStoreObj->getWebsiteId()) {
            $this->_currentStore = $store;
        } elseif ($type == 'group' && $storeObj->getGroupId() == $curStoreObj->getGroupId()) {
            $this->_currentStore = $store;
        } elseif ($type == 'store') {
            $this->_currentStore = $store;
        }

        if ($this->_currentStore == $store) {
            $store = $this->getStore($store);
            if ($store->getWebsite()->getDefaultStore()->getId() == $store->getId()) {
                $this->getCookie()->delete(Mage_Core_Model_Store::COOKIE_NAME);
            } else {
                $this->getCookie()->set(Mage_Core_Model_Store::COOKIE_NAME, $this->_currentStore, true);
            }
        }
        return $this;
    }

    /**
     * Check cookie store
     *
     * @param string $type
     * @return Mage_Core_Model_App
     */
    protected function _checkCookieStore($type)
    {
        if (!$this->getCookie()->get()) {
            return $this;
        }

        $store = $this->getCookie()->get(Mage_Core_Model_Store::COOKIE_NAME);
        if ($store && isset($this->_stores[$store])
            && $this->_stores[$store]->getId()
            && $this->_stores[$store]->getIsActive()
        ) {
            if ($type == 'website'
                && $this->_stores[$store]->getWebsiteId() == $this->_stores[$this->_currentStore]->getWebsiteId()
            ) {
                $this->_currentStore = $store;
            }
            if ($type == 'group'
                && $this->_stores[$store]->getGroupId() == $this->_stores[$this->_currentStore]->getGroupId()
            ) {
                $this->_currentStore = $store;
            }
            if ($type == 'store') {
                $this->_currentStore = $store;
            }
        }
        return $this;
    }

    public function reinitStores()
    {
        $this->_initStores();
    }

    /**
     * Init store, group and website collections
     */
    protected function _initStores()
    {
        $this->_stores   = array();
        $this->_groups   = array();
        $this->_website  = null;
        $this->_websites = array();

        /** @var $websiteCollection Mage_Core_Model_Website */
        $websiteCollection = Mage::getModel('Mage_Core_Model_Website')->getCollection()
                ->initCache($this->getCache(), 'app', array(Mage_Core_Model_Website::CACHE_TAG))
                ->setLoadDefault(true);

        /** @var $websiteCollection Mage_Core_Model_Store_Group */
        $groupCollection = Mage::getModel('Mage_Core_Model_Store_Group')->getCollection()
                ->initCache($this->getCache(), 'app', array(Mage_Core_Model_Store_Group::CACHE_TAG))
                ->setLoadDefault(true);

        /** @var $websiteCollection Mage_Core_Model_Store */
        $storeCollection = Mage::getModel('Mage_Core_Model_Store')->getCollection()
            ->initCache($this->getCache(), 'app', array(Mage_Core_Model_Store::CACHE_TAG))
            ->setLoadDefault(true);

        $this->_hasSingleStore = false;
        if ($this->_isSingleStoreAllowed) {
            $this->_hasSingleStore = $storeCollection->count() < 3;
        }

        $websiteStores = array();
        $websiteGroups = array();
        $groupStores   = array();

        foreach ($storeCollection as $store) {
            /** @var $store Mage_Core_Model_Store */
            $store->initConfigCache();
            $store->setWebsite($websiteCollection->getItemById($store->getWebsiteId()));
            $store->setGroup($groupCollection->getItemById($store->getGroupId()));

            $this->_stores[$store->getId()] = $store;
            $this->_stores[$store->getCode()] = $store;

            $websiteStores[$store->getWebsiteId()][$store->getId()] = $store;
            $groupStores[$store->getGroupId()][$store->getId()] = $store;

            if (is_null($this->_store) && $store->getId()) {
                $this->_store = $store;
            }
        }

        foreach ($groupCollection as $group) {
            /* @var $group Mage_Core_Model_Store_Group */
            if (!isset($groupStores[$group->getId()])) {
                $groupStores[$group->getId()] = array();
            }
            $group->setStores($groupStores[$group->getId()]);
            $group->setWebsite($websiteCollection->getItemById($group->getWebsiteId()));

            $websiteGroups[$group->getWebsiteId()][$group->getId()] = $group;

            $this->_groups[$group->getId()] = $group;
        }

        foreach ($websiteCollection as $website) {
            /* @var $website Mage_Core_Model_Website */
            if (!isset($websiteGroups[$website->getId()])) {
                $websiteGroups[$website->getId()] = array();
            }
            if (!isset($websiteStores[$website->getId()])) {
                $websiteStores[$website->getId()] = array();
            }
            if ($website->getIsDefault()) {
                $this->_website = $website;
            }
            $website->setGroups($websiteGroups[$website->getId()]);
            $website->setStores($websiteStores[$website->getId()]);

            $this->_websites[$website->getId()] = $website;
            $this->_websites[$website->getCode()] = $website;
        }
    }

    /**
     * Check if system is run in the single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        $this->_initializeStore();
        return $this->hasSingleStore() && Mage::helper('Mage_Core_Helper_Data')->isSingleStoreModeEnabled();
    }

    /**
     * Check if store has only one store view
     *
     * @return bool
     */
    public function hasSingleStore()
    {
        if (!Mage::isInstalled()) {
            return false;
        }
        $this->_initializeStore();
        return $this->_hasSingleStore;
    }

    /**
     * Retrieve store code or null by store group
     *
     * @param int $group
     * @return string|null
     */
    protected function _getStoreByGroup($group)
    {
        if (!isset($this->_groups[$group])) {
            return null;
        }
        if (!$this->_groups[$group]->getDefaultStoreId()) {
            return null;
        }
        return $this->_stores[$this->_groups[$group]->getDefaultStoreId()]->getCode();
    }

    /**
     * Retrieve store code or null by website
     *
     * @param int|string $website
     * @return string|null
     */
    protected function _getStoreByWebsite($website)
    {
        if (!isset($this->_websites[$website])) {
            return null;
        }
        if (!$this->_websites[$website]->getDefaultGroupId()) {
            return null;
        }
        return $this->_getStoreByGroup($this->_websites[$website]->getDefaultGroupId());
    }

    /**
     * Set current default store
     *
     * @param string $store
     * @return Mage_Core_Model_App
     */
    public function setCurrentStore($store)
    {
        $this->_currentStore = $store;
        return $this;
    }

    /**
     * Initialize application front controller
     *
     * @return Mage_Core_Model_App
     */
    protected function _initFrontController()
    {
        $this->_frontController = $this->_getFrontControllerByCurrentArea();
        Magento_Profiler::start('init_front_controller');
        $this->_frontController->init();
        Magento_Profiler::stop('init_front_controller');
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
                    $this->getConfig()->setCurrentAreaCode($areaCode);
                    $frontControllerClass = $areaInfo['front_controller'];
                    break;
                }
            }
        }
        return $this->_objectManager->create($frontControllerClass);
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
     * Retrieve application store object
     *
     * @param null|string|bool|int|Mage_Core_Model_Store $id
     * @return Mage_Core_Model_Store
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getStore($id = null)
    {
        if (!Mage::isInstalled() || Mage::getUpdateMode()) {
            return $this->_getDefaultStore();
        }
        $this->_initializeStore();

        if ($id === true && $this->hasSingleStore()) {
            return $this->_store;
        }

        if (!isset($id) || '' === $id || $id === true) {
            if (null === $this->_currentStore) {
                $this->_initCurrentStore($this->_scopeCode, $this->_scopeType ?: self::SCOPE_TYPE_STORE);
                $this->_log->initForStore($this->_store, $this->_config);
            }
            $id = $this->_currentStore;
        }
        if ($id instanceof Mage_Core_Model_Store) {
            return $id;
        }
        if (!isset($id)) {
            $this->throwStoreException();
        }

        if (empty($this->_stores[$id])) {
            $store = Mage::getModel('Mage_Core_Model_Store');
            /* @var $store Mage_Core_Model_Store */
            if (is_numeric($id)) {
                $store->load($id);
            } elseif (is_string($id)) {
                $store->load($id, 'code');
            }

            if (!$store->getCode()) {
                $this->throwStoreException();
            }
            $this->_stores[$store->getStoreId()] = $store;
            $this->_stores[$store->getCode()] = $store;
        }
        return $this->_stores[$id];
    }

    /**
     * Retrieve application store object without Store_Exception
     *
     * @param string|int|Mage_Core_Model_Store $id
     * @return Mage_Core_Model_Store
     */
    public function getSafeStore($id = null)
    {
        try {
            return $this->getStore($id);
        } catch (Exception $e) {
            if ($this->_currentStore) {
                $this->getRequest()->setActionName('noRoute');
                return new Varien_Object();
            } else {
                Mage::throwException(Mage::helper('Mage_Core_Helper_Data')->__('Requested invalid store "%s"', $id));
            }
        }
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
        $this->_initializeStore();
        $stores = array();
        foreach ($this->_stores as $store) {
            if (!$withDefault && $store->getId() == 0) {
                continue;
            }
            if ($codeKey) {
                $stores[$store->getCode()] = $store;
            } else {
                $stores[$store->getId()] = $store;
            }
        }

        return $stores;
    }

    protected function _getDefaultStore()
    {
        if (empty($this->_store)) {
            $this->_store = Mage::getModel('Mage_Core_Model_Store')
                ->setId(self::DISTRO_STORE_ID)
                ->setCode(self::DISTRO_STORE_CODE);
        }
        return $this->_store;
    }

    /**
     * Retrieve default store for default group and website
     *
     * @return Mage_Core_Model_Store
     */
    public function getDefaultStoreView()
    {
        $this->_initializeStore();
        foreach ($this->getWebsites() as $_website) {
            if ($_website->getIsDefault()) {
                $_defaultStore = $this->getGroup($_website->getDefaultGroupId())->getDefaultStore();
                if ($_defaultStore) {
                    return $_defaultStore;
                }
            }
        }
        return null;
    }

    public function getDistroLocaleCode()
    {
        return self::DISTRO_LOCALE_CODE;
    }

    /**
     * Retrieve application website object
     *
     * @param null|bool|int|string|Mage_Core_Model_Website $id
     * @return Mage_Core_Model_Website
     * @throws Mage_Core_Exception
     */
    public function getWebsite($id = null)
    {
        $this->_initializeStore();
        if (is_null($id)) {
            $id = $this->getStore()->getWebsiteId();
        } elseif ($id instanceof Mage_Core_Model_Website) {
            return $id;
        } elseif ($id === true) {
            return $this->_website;
        }

        if (empty($this->_websites[$id])) {
            $website = Mage::getModel('Mage_Core_Model_Website');
            if (is_numeric($id)) {
                $website->load($id);
                if (!$website->hasWebsiteId()) {
                    throw Mage::exception('Mage_Core', 'Invalid website id requested.');
                }
            } elseif (is_string($id)) {
                $websiteConfig = $this->_config->getNode('websites/' . $id);
                if (!$websiteConfig) {
                    throw Mage::exception('Mage_Core', 'Invalid website code requested: ' . $id);
                }
                $website->loadConfig($id);
            }
            $this->_websites[$website->getWebsiteId()] = $website;
            $this->_websites[$website->getCode()] = $website;
        }
        return $this->_websites[$id];
    }

    public function getWebsites($withDefault = false, $codeKey = false)
    {
        $this->_initializeStore();
        $websites = array();
        if (is_array($this->_websites)) {
            foreach ($this->_websites as $website) {
                if (!$withDefault && $website->getId() == 0) {
                    continue;
                }
                if ($codeKey) {
                    $websites[$website->getCode()] = $website;
                } else {
                    $websites[$website->getId()] = $website;
                }
            }
        }

        return $websites;
    }

    /**
     * Retrieve application store group object
     *
     * @param null|Mage_Core_Model_Store_Group|string $id
     * @return Mage_Core_Model_Store_Group
     * @throws Mage_Core_Exception
     */
    public function getGroup($id = null)
    {
        $this->_initializeStore();
        if (is_null($id)) {
            $id = $this->getStore()->getGroupId();
        } elseif ($id instanceof Mage_Core_Model_Store_Group) {
            return $id;
        }
        if (empty($this->_groups[$id])) {
            $group = Mage::getModel('Mage_Core_Model_Store_Group');
            if (is_numeric($id)) {
                $group->load($id);
                if (!$group->hasGroupId()) {
                    throw Mage::exception('Mage_Core', 'Invalid store group id requested.');
                }
            }
            $this->_groups[$group->getGroupId()] = $group;
        }
        return $this->_groups[$id];
    }

    /**
     * Retrieve application locale object
     *
     * @return Mage_Core_Model_Locale
     */
    public function getLocale()
    {
        if (!$this->_locale) {
            $this->_locale = Mage::getSingleton('Mage_Core_Model_Locale');
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
        if (!$this->_layout) {
            if ($this->getFrontController()->getAction()) {
                $this->_layout = $this->getFrontController()->getAction()->getLayout();
            } else {
                $this->_layout = Mage::getSingleton('Mage_Core_Model_Layout');
            }
        }
        return $this->_layout;
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
        return (string) Mage::app()->getConfig()
            ->getNode('default/' . Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE);
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
     * @return Mage_Core_Model_Cache
     */
    public function getCacheInstance()
    {
        return $this->_cache;
    }

    /**
     * Retrieve cache object
     *
     * @return Zend_Cache_Core
     */
    public function getCache()
    {
        return $this->_cache->getFrontend();
    }

    /**
     * Loading cache data
     *
     * @param   string $id
     * @return  mixed
     */
    public function loadCache($id)
    {
        return $this->_cache->load($id);
    }

    /**
     * Saving cache data
     *
     * @param mixed $data
     * @param string $id
     * @param array $tags
     * @param bool $lifeTime
     * @return Mage_Core_Model_App
     */
    public function saveCache($data, $id, $tags = array(), $lifeTime = false)
    {
        $this->_cache->save($data, $id, $tags, $lifeTime);
        return $this;
    }

    /**
     * Remove cache
     *
     * @param   string $id
     * @return  Mage_Core_Model_App
     */
    public function removeCache($id)
    {
        $this->_cache->remove($id);
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
        Mage::dispatchEvent('application_clean_cache', array('tags' => $tags));
        return $this;
    }

    /**
     * Check whether to use cache for specific component
     *
     * @param null|string $type
     * @return boolean
     */
    public function useCache($type = null)
    {
        return $this->_cache->canUse($type);
    }

    /**
     * Save cache usage settings
     *
     * @param array $data
     * @return Mage_Core_Model_App
     */
    public function saveUseCache($data)
    {
        $this->_cache->saveOptions($data);
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
            $dir = session_save_path();
            mageDelTree($dir);
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
            $this->_request = Mage::getObjectManager()->get('Mage_Core_Controller_Request_Http');
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

    public function throwStoreException()
    {
        throw new Mage_Core_Model_Store_Exception('');
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
     * Get either default or any store view
     *
     * @return Mage_Core_Model_Store
     */
    public function getAnyStoreView()
    {
        $store = $this->getDefaultStoreView();
        if ($store) {
            return $store;
        }
        foreach ($this->getStores() as $store) {
            return $store;
        }
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
     * Allow or disallow single store mode
     *
     * @param bool $value
     * @return Mage_Core_Model_App
     */
    public function setIsSingleStoreModeAllowed($value)
    {
        $this->_isSingleStoreAllowed = (bool)$value;
        return $this;
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
        $this->_initializeStore();
        $groups = array();
        if (is_array($this->_groups)) {
            foreach ($this->_groups as $group) {
                if (!$withDefault && $group->getId() == 0) {
                    continue;
                }
                if ($codeKey) {
                    $groups[$group->getCode()] = $group;
                } else {
                    $groups[$group->getId()] = $group;
                }
            }
        }
        return $groups;
    }

    /**
     * Get is cache locked
     *
     * @return bool
     */
    public function getIsCacheLocked()
    {
        return (bool)$this->_isCacheLocked;
    }

    /**
     *  Unset website by id from app cache
     *
     * @param null|bool|int|string|Mage_Core_Model_Website $id
     */
    public function clearWebsiteCache($id = null)
    {
        if (is_null($id)) {
            $id = $this->getStore()->getWebsiteId();
        } elseif ($id instanceof Mage_Core_Model_Website) {
            $id = $id->getId();
        } elseif ($id === true) {
            $id = $this->_website->getId();
        }

        if (!empty($this->_websites[$id])) {
            $website = $this->_websites[$id];

            unset($this->_websites[$website->getWebsiteId()]);
            unset($this->_websites[$website->getCode()]);
        }
    }

    /**
     * Check if developer mode is enabled.
     *
     * @return bool
     */
    public function isDeveloperMode()
    {
        return Mage::getIsDeveloperMode();
    }
}
