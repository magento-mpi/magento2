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
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Application model
 *
 * Application should have: areas, store, locale, translator, design package
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Core_Model_App
{
    const XML_PATH_INSTALL_DATE = 'global/install/date';

    const DEFAULT_ERROR_HANDLER = 'mageCoreErrorHandler';

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
     * Application configuration object
     *
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * Application front controller
     *
     * @var Mage_Core_Controller_Varien_Front
     */
    protected $_frontController;

    /**
     * Cache object
     *
     * @var Zend_Cache_Core
     */
    protected $_cache;

    /**
     * Helpers array
     *
     * @var array
     */
    protected $_helpers = array();

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
    protected $_websites;

    /**
     * Groups cache
     *
     * @var array
     */
    protected $_groups;

    /**
     * Stores cache
     *
     * @var array
     */
    protected $_stores;

    /**
     * is a single store mode
     *
     * @var bool
     */
    protected $_isSingleStore;

    /**
     * Default store code
     *
     * @var string
     */
    protected $_currentStore;

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
     * Constructor
     *
     */
    public function __construct() {}

    /**
     * Initialize application
     *
     * @param string $code
     * @param string $type
     * @param string $etcDir
     * @return Mage_Core_Model_App
     */
    public function init($code, $type, $etcDir)
    {
        Varien_Profiler::start('app/construct');

        $this->setErrorHandler(self::DEFAULT_ERROR_HANDLER);
        date_default_timezone_set(Mage_Core_Model_Locale::DEFAULT_TIMEZONE);

        $this->_config = Mage::getConfig();
        $this->_config->init($etcDir);

        if ($this->isInstalled()) {
            $this->_initStores();

            switch ($type) {
                case 'store':
                    $this->_currentStore = $code;
                    break;
                case 'group':
                    $this->_currentStore = $this->_getStoreByGroup($code);
                    break;
                case 'website':
                    $this->_currentStore = $this->_getStoreByWebsite($code);
                    break;
                default:
                    $this->_printError('Invalid Type! Allowed types: website, group, store');
            }

            $cookie = Mage::getSingleton('core/cookie');
            $store  = $cookie->get('store');
            if (isset($_GET['store'])) {
                $newStore = $_GET['store'];
                if ($newStore != $store && isset($this->_stores[$newStore])
                    && $this->_stores[$newStore]->getId() && $this->_stores[$newStore]->getIsActive()) {
                    $cookie->set('store', $newStore);
                    $store = $newStore;
                }
            }
            if ($store && isset($this->_stores[$store])
                && $this->_stores[$store]->getId() && $this->_stores[$store]->getIsActive()) {
                $this->_currentStore = $store;
            }
        }

        Varien_Profiler::stop('app/construct');
        return $this;
    }

    /**
     * Init store, group and website collections
     *
     */
    protected function _initStores()
    {
        $websiteCollection = Mage::getModel('core/website')->getCollection()->setLoadDefault(true);
        $groupCollection = Mage::getModel('core/store_group')->getCollection()->setLoadDefault(true);
        $storeCollection = Mage::getModel('core/store')->getCollection()->setLoadDefault(true);

        $this->_isSingleStore = $storeCollection->getSize() < 3;

        $websiteStores = array();
        $websiteGroups = array();
        $groupStores   = array();

        foreach ($storeCollection as $store) {
            /* @var $store Mage_Core_Model_Store */
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
            $website->setGroups($websiteGroups[$website->getId()]);
            $website->setStores($websiteStores[$website->getId()]);

            $this->_websites[$website->getId()] = $website;
            $this->_websites[$website->getCode()] = $website;
        }
    }

    /**
     * Is single Store mode (only one store without default)
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        if (!$this->isInstalled()) {
            return false;
        }
        return $this->_isSingleStore;
    }

    protected function _getStoreByGroup($group)
    {
        if (!isset($this->_groups[$group])) {
            $this->_printError('Invalid Store "' . $group . '" requested');
        }
        if (!$this->_groups[$group]->getDefaultStoreId()) {
            $this->_printError('There are no languages available for "' . $this->_groups[$group]->getName() . '"');
        }
        return $this->_groups[$group]->getDefaultStoreId();
    }

    protected function _getStoreByWebsite($website)
    {
        if (!isset($this->_websites[$website])) {
            $this->_printError('Invalid Website "' . $website . '" requested');
        }
        if (!$this->_websites[$website]->getDefaultGroupId()) {
            $this->_printError('There are no stores available for "' . $this->_websites[$website]->getName() . '"');
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
        $this->_frontController = new Mage_Core_Controller_Varien_Front();
        Mage::register('controller', $this->_frontController);
        $this->_frontController->init();
        return $this;
    }

    /**
     * Redeclare custom error handler
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
     * Loding part of area data
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
            $this->_areas[$code] = new Mage_Core_Model_App_Area($code, $this);
        }
        return $this->_areas[$code];
    }

    /**
     * Retrieve application store object
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore($id=null)
    {
        if (!$this->isInstalled()) {
            return $this->_getDefaultStore();
        }

        if ($id === true && $this->isSingleStoreMode()) {
            return $this->_store;
        }

        if (is_null($id) || ''===$id) {
            $id = $this->_currentStore;
        }
        if ($id instanceof Mage_Core_Model_Store) {
            return $id;
        }

        if (empty($this->_stores[$id])) {
            $store = Mage::getModel('core/store');
            /* @var $store Mage_Core_Model_Store */
            if (is_numeric($id)) {
                $store->load($id);
            } elseif (is_string($id)) {
                $store->load($id, 'code');
            }
            if (!$store->getCode()) {
                $this->_printError('There is no default store on website "'.$id.'".');
            }
            $this->_stores[$store->getStoreId()] = $store;
            $this->_stores[$store->getCode()] = $store;
        }
        return $this->_stores[$id];
    }

    protected function _getDefaultStore()
    {
        if (empty($this->_store)) {
            $this->_store = new Mage_Core_Model_Store();
            $this->_store->setStoreId(1)->setCode('default');
        }
        return $this->_store;
    }

    /**
     * Retrieve application website object
     *
     * @return Mage_Core_Model_Website
     */
    public function getWebsite($id=null)
    {
        if (is_null($id)) {
            $id = $this->getStore()->getWebsiteId();
        } elseif ($id instanceof Mage_Core_Model_Website) {
            return $id;
        }
        if (empty($this->_websites[$id])) {
            $website = Mage::getModel('core/website');
            if (is_numeric($id)) {
                $website->load($id);
                if (!$website->hasWebsiteId()) {
                    throw Mage::exception('Mage_Core', 'Invalid website id requested.');
                }
            } elseif (is_string($id)) {
                $websiteConfig = Mage::getConfig()->getNode('websites/'.$id);
                if (!$websiteConfig) {
                    throw Mage::exception('Mage_Core', 'Invalid website code requested: '.$id);
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
        $websites = array();
        foreach ($this->_websites as $website) {
            if (!$withDefault && $website->getId() == 0) {
                continue;
            }
            if ($codeKey) {
                $websites[$website->getCode()] = $website;
            }
            else {
                $websites[$website->getId()] = $website;
            }
        }

        return $websites;
    }

    /**
     * Retrieve application store group object
     *
     * @return Mage_Core_Model_Store_Group
     */

    public function getGroup($id=null)
    {
        if (is_null($id)) {
            $id = $this->getStore()->getGroup()->getId();
        } elseif ($id instanceof Mage_Core_Model_Store_Group) {
            return $id;
        }
        if (empty($this->_groups[$id])) {
            $group = Mage::getModel('core/store_group');
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
            $this->_locale = Mage::getSingleton('core/locale');
        }
        return $this->_locale;
    }

    /**
     * Retrieve translate object
     *
     * @return Mage_Core_Model_Translate
     */
    public function getTranslator()
    {
        if (!$this->_translator) {
            $this->_translator = Mage::getSingleton('core/translate');
        }
        return $this->_translator;
    }

    /**
     * Retrieve helper object
     *
     * @param   helper name $name
     * @return  Mage_Core_Helper_Abstract
     */
    public function getHelper($name)
    {
        if (!isset($this->_helpers[$name])) {
            $class = Mage::getConfig()->getHelperClassName($name);
            $this->_helpers[$name] = new $class();
        }
        return $this->_helpers[$name];
    }

    /**
     * Retrieve application base currency code
     *
     * @return string
     */
    public function getBaseCurrencyCode()
    {
        return Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE, $this->getStore()->getCode());
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
        if (!$this->_frontController) {
            $this->_initFrontController();
        }
        return $this->_frontController;
    }

    /**
     * Retrieve application installation flag
     *
     * @return bool
     */
    public function isInstalled()
    {
        $installDate = Mage::getConfig()->getNode(self::XML_PATH_INSTALL_DATE);
        if ($installDate && strtotime($installDate)) {
            return true;
        }
        return false;
    }

    /**
     * Generate cache id with application specific data
     *
     * @param   string $id
     * @return  string
     */
    protected function _getCacheId($id=null)
    {
        if ($id) {
            $id = strtoupper($id);
        }
        return $id;
    }

    /**
     * Generate cache tags from cache id
     *
     * @param   string $id
     * @param   array $tags
     * @return  array
     */
    protected function _getCacheIdTags($id, $tags=array())
    {
        return $tags;

        $idTags = explode('_', $id);

        $first = true;
        foreach ($idTags as $tag) {
            $newTag = $first ? $tag : $newTag . '_' . $tag;
            if (!in_array($newTag, $tags)) {
                $tags[] = $newTag;
            }
            $first = false;
        }

        return $tags;
    }

    /**
     * Retrieve cache object
     *
     * @return Zend_Cache_Core
     */
    public function getCache()
    {
        if (!$this->_cache) {
            $this->_cache = Zend_Cache::factory('Core', 'File',
                array('caching'=>true, 'lifetime'=>7200),
                array(
                    'cache_dir'=>Mage::getBaseDir('cache'),
                    'hashed_directory_level'=>1,
                    'hashed_directory_umask'=>0777,
                    'file_name_prefix'=>'mage')
            );
        }
        return $this->_cache;
    }

    /**
     * Loading cache data
     *
     * @param   string $id
     * @return  mixed
     */
    public function loadCache($id)
    {
        return $this->getCache()->load($this->_getCacheId($id));
    }

    /**
     * Saving cache data
     *
     * @param   mixed $data
     * @param   string $id
     * @param   array $tags
     * @return  Mage_Core_Model_App
     */
    public function saveCache($data, $id, $tags=array(), $lifeTime=false)
    {
        $this->getCache()->save((string)$data, $this->_getCacheId($id), $this->_getCacheIdTags($id, $tags), $lifeTime);
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
        $this->getCache()->remove($this->_getCacheId($id));
        return $this;
    }

    /**
     * Cleaning cache
     *
     * @param   array $tags
     * @return  Mage_Core_Model_App
     */
    public function cleanCache($tags=array())
    {
        if (!empty($tags)) {
            $this->getCache()->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, $tags);
        } else {
            $useCache = $this->useCache();

            $cacheDir = Mage::getBaseDir('var').DS.'cache';
            mageDelTree($cacheDir);
            mkdir($cacheDir, 0777);

            $this->saveCache(serialize($useCache), 'use_cache', array(), null);
        }
        return $this;
    }

    /**
    * Check whether to use cache for specific component
    *
    * Components:
    * - config
    * - layout
    * - eav
    * - translate
    *
    * @return boolean
    */
    public function useCache($type=null)
    {
        if (!$this->_useCache) {
            $data = $this->loadCache('use_cache');
            if (is_string($data)) {
                $this->_useCache = unserialize($data);
            } else {
                $this->_useCache = array();
            }
        }
        if (empty($type)) {
            return $this->_useCache;
        } else {
            return isset($this->_useCache[$type]) ? (bool)$this->_useCache[$type] : false;
        }
    }

    /**
     * Retrieve request object
     *
     * @return Mage_Core_Controller_Request_Http
     */
    public function getRequest()
    {
        if (empty($this->_request)) {
            $this->_request = new Mage_Core_Controller_Request_Http();
        }
        return $this->_request;
    }

    /**
     * Retrieve response object
     *
     * @return Zend_Controller_Response_Http
     */
    public function getResponse()
    {
        if (empty($this->_response)) {
            $this->_response = new Zend_Controller_Response_Http();
            $this->_response->setHeader("Content-Type", "text/html; charset=UTF-8");
        }
        return $this->_response;
    }

    protected function _printError($message)
    {
        die('<div style="font:12px/1.35em arial, helvetica, sans-serif;"><div style="margin:0 0 25px 0; border-bottom:1px solid #ccc;"><h3 style="margin:0; font-size:1.7em; font-weight:normal; text-transform:none; text-align:left; color:#2f2f2f;">Configuration error.</h3></div><p>'.$message.'</p></div>');
    }
}
