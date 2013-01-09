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
 * Core configuration class
 */
class Mage_Core_Model_Config
{
    const CACHE_TAG = 'CONFIG';

    /**
     * Read additional file during initialization
     */
    const INIT_OPTION_EXTRA_FILE = 'MAGE_CONFIG_FILE';

    /**
     * Read additional data (XML-string) during initialization
     */
    const INIT_OPTION_EXTRA_DATA = 'MAGE_CONFIG_DATA';

    /**
     * Local configuration file
     */
    const LOCAL_CONFIG_FILE = 'local.xml';

    /**
     * Application installation date
     */
    const XML_PATH_INSTALL_DATE = 'global/install/date';

    /**
     * Configuration template for the application installation date
     */
    const CONFIG_TEMPLATE_INSTALL_DATE = '<config><global><install><date>%s</date></install></global></config>';

    /**
     * Flag which allow use cache logic
     *
     * @var bool
     */
    protected $_useCache = false;

    /**
     * Instructions for spitting config cache
     * array(
     *      $sectionName => $recursionLevel
     * )
     * Recursion level provide availability cache subnodes separatly
     *
     * @var array
     */
    protected $_cacheSections = array(
        'admin'     => 0,
        'adminhtml' => 0,
        'crontab'   => 0,
        'install'   => 0,
        'stores'    => 1,
        'websites'  => 0
    );

    /**
     * Loaded Configuration by cached sections
     *
     * @var array
     */
    protected $_cacheLoadedSections = array();

    /**
     * Storage for generated class names
     *
     * @var array
     */
    protected $_classNameCache = array();

    /**
     * Storage for generated block class names
     *
     * @var array
     */
    protected $_blockClassNameCache = array();

    /**
     * Storage of validated secure urls
     *
     * @var array
     */
    protected $_secureUrlCache = array();

    /**
     * Resource model
     * Used for operations with DB
     *
     * @var Mage_Core_Model_Resource_Config
     */
    protected $_resourceModel;

    /**
     * Configuration for events by area
     *
     * @var array
     */
    protected $_eventAreas;

    /**
     * Flag cache for existing or already created directories
     *
     * @var array
     */
    protected $_dirExists = array();

    /**
     * Flach which allow using cache for config initialization
     *
     * @var bool
     */
    protected $_allowCacheForInit = true;

    /**
     * Property used during cache save process
     *
     * @var array
     */
    protected $_cachePartsForSave = array();

    /**
     * Empty configuration object for loading and merging configuration parts
     *
     * @var Mage_Core_Model_Config_Base
     */
    protected $_prototype;

    /**
     * Whether local configuration is loaded or not
     *
     * @var bool
     */
    protected $_isLocalConfigLoaded = false;

    /**
     * Active modules array per namespace
     * @var array
     */
    private $_moduleNamespaces = null;

    /**
     * Modules allowed to load
     * If empty - all modules are allowed
     *
     * @var array
     */
    protected $_allowedModules = array();

    /**
     * Areas allowed to use
     *
     * @var array
     */
    protected $_allowedAreas = null;

    /**
     * Paths to module's directories (etc, sql, locale etc)
     *
     * @var array
     */
    protected $_moduleDirs = array();

    /*
     * Cache for declared modules to prevent loading modules' config twice
     *
     * @var array
     */
    protected $_modulesCache = array();

    /**
     * Current area code
     *
     * @var string
     */
    protected $_currentAreaCode = null;

    /**
     * Object manager
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Application installation timestamp
     *
     * @var int|null
     */
    protected $_installDate;

    /**
     * @var Mage_Core_Model_CacheInterface
     */
    protected $_cacheModel;

    /**
     * @var Mage_Core_Model_Config_StorageInterface
     */
    protected $_storage;

    /**
     * Class construct
     *
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Model_CacheInterface $cache
     * @param mixed $sourceData
     */
    public function __construct(
        Mage_Core_Model_Config_StorageInterface $configStorage,
        Magento_ObjectManager $objectManager,
        Mage_Core_Model_CacheInterface $cache
    ) {
//        $this->_objectManager = $objectManager;
        $this->setCacheId('config_global');
//        $this->_prototype = $this->_objectManager->create('Mage_Core_Model_Config_Base');
//        $this->_prototype->loadString('<config/>');
//        $this->_cacheChecksum = null;
//        $this->_cacheModel = $cache;
        $this->_data = $this->_storage->getConfiguration();
        $this->_loadInstallDate();
    }

    /**
     * Load application installation date
     */
    protected function _loadInstallDate()
    {
        $installDateNode = $this->getNode(self::XML_PATH_INSTALL_DATE);
        if ($installDateNode) {
            $this->_installDate = strtotime((string)$installDateNode);
        }
    }

    /**
     * Retrieve application installation date as a timestamp or NULL, if it has not been installed yet
     *
     * @return int|null
     */
    public function getInstallDate()
    {
        return $this->_installDate;
    }


    /**
     * Reinitialize configuration
     *
     * @return Mage_Core_Model_Config
     */
    public function reinit()
    {
        $this->_data = $this->_storage->getConfiguration(false);
    }

    /**
     * Remove configuration cache
     *
     * @return Mage_Core_Model_Config
     */
    public function removeCache()
    {
        $tags = array(self::CACHE_TAG);
        $this->_cacheModel->clean($tags);
        Mage::dispatchEvent('application_clean_cache', array('tags' => $tags));
        return parent::removeCache();
    }

    /**
     * Getter for section configuration object
     *
     * @param array $path
     * @return Mage_Core_Model_Config_Element
     */
    protected function _getSectionConfig($path)
    {
        $section = $path[0];
        if (!isset($this->_cacheSections[$section])) {
            return false;
        }
        $sectionPath = array_slice($path, 0, $this->_cacheSections[$section]+1);
        $sectionKey = implode('_', $sectionPath);

        if (!isset($this->_cacheLoadedSections[$sectionKey])) {
            Magento_Profiler::start('init_config_section:' . $sectionKey);
            $this->_cacheLoadedSections[$sectionKey] = $this->_loadSectionCache($sectionKey);
            Magento_Profiler::stop('init_config_section:' . $sectionKey);
        }

        if ($this->_cacheLoadedSections[$sectionKey] === false) {
            return false;
        }
        return $this->_cacheLoadedSections[$sectionKey];
    }

    /**
     * Get node value from cached section data
     *
     * @param   array $path
     * @return  Mage_Core_Model_Config
     */
    public function getSectionNode($path)
    {
        $section    = $path[0];
        $config     = $this->_getSectionConfig($path);
        $path       = array_slice($path, $this->_cacheSections[$section] + 1);
        if ($config) {
            return $config->descend($path);
        }
        return false;
    }

    /**
     * Returns node found by the $path and scope info
     *
     * @param   string $path
     * @param   string $scope
     * @param   string|int $scopeCode
     * @return Mage_Core_Model_Config_Element
     */
    public function getNode($path=null, $scope='', $scopeCode=null)
    {
        if ($scope !== '') {
            if (('store' === $scope) || ('website' === $scope)) {
                $scope .= 's';
            }
            if (('default' !== $scope) && is_int($scopeCode)) {
                if ('stores' == $scope) {
                    $scopeCode = Mage::app()->getStore($scopeCode)->getCode();
                } elseif ('websites' == $scope) {
                    $scopeCode = Mage::app()->getWebsite($scopeCode)->getCode();
                } else {
                    Mage::throwException(Mage::helper('Mage_Core_Helper_Data')->__('Unknown scope "%s".', $scope));
                }
            }
            $path = $scope . ($scopeCode ? '/' . $scopeCode : '' ) . (empty($path) ? '' : '/' . $path);
        }

        /**
         * Check path cache loading
         */
        if ($this->_useCache && ($path !== null)) {
            $path   = explode('/', $path);
            $section= $path[0];
            if (isset($this->_cacheSections[$section])) {
                $res = $this->getSectionNode($path);
                if ($res !== false) {
                    return $res;
                }
            }
        }
        return  parent::getNode($path);
    }

    /**
     * Create node by $path and set its value.
     *
     * @param string $path separated by slashes
     * @param string $value
     * @param bool $overwrite
     * @return Varien_Simplexml_Config
     */
    public function setNode($path, $value, $overwrite = true)
    {
        if ($this->_useCache && ($path !== null)) {
            $sectionPath = explode('/', $path);
            $config = $this->_getSectionConfig($sectionPath);
            if ($config) {
                $sectionPath = array_slice($sectionPath, $this->_cacheSections[$sectionPath[0]]+1);
                $sectionPath = implode('/', $sectionPath);
                $config->setNode($sectionPath, $value, $overwrite);
            }
        }
        return parent::setNode($path, $value, $overwrite);
    }

    /**
     * Determine whether provided name begins from any available modules, according to namespaces priority
     * If matched, returns as the matched module "factory" name or a fully qualified module name
     *
     * @param string $name
     * @param bool $asFullModuleName
     * @return string
     */
    public function determineOmittedNamespace($name, $asFullModuleName = false)
    {
        if (null === $this->_moduleNamespaces) {
            $this->_moduleNamespaces = array();
            foreach ($this->_xml->xpath('modules/*') as $m) {
                if ((string)$m->active == 'true') {
                    $moduleName = $m->getName();
                    $module = strtolower($moduleName);
                    $this->_moduleNamespaces[substr($module, 0, strpos($module, '_'))][$module] = $moduleName;
                }
            }
        }

        $name = explode('_', strtolower($name));
        $partsNum = count($name);
        $defaultNamespaceFlag = false;
        foreach ($this->_moduleNamespaces as $namespaceName => $namespace) {
            // assume the namespace is omitted (default namespace only, which comes first)
            if ($defaultNamespaceFlag === false) {
                $defaultNamespaceFlag = true;
                $defaultNS = $namespaceName . '_' . $name[0];
                if (isset($namespace[$defaultNS])) {
                    return $asFullModuleName ? $namespace[$defaultNS] : $name[0]; // return omitted as well
                }
            }
            // assume namespace is qualified
            if(isset($name[1])) {
                $fullNS = $name[0] . '_' . $name[1];
                if (2 <= $partsNum && isset($namespace[$fullNS])) {
                    return $asFullModuleName ? $namespace[$fullNS] : $fullNS;
                }
            }
        }
        return '';
    }

    /**
     * Iterate all active modules "etc" folders and combine data from
     * specidied xml file name to one object
     *
     * @param   string $fileName
     * @param   null|Mage_Core_Model_Config_Base $mergeToObject
     * @return  Mage_Core_Model_Config_Base
     */
    public function loadModulesConfiguration($fileName, $mergeToObject = null, $mergeModel=null)
    {
       return $this->_storage->loadModulesConfiguration($fileName, $mergeToObject, $mergeModel);
    }

    /**
     * Go through all modules and find configuration files of active modules
     *
     * @param string $filename
     * @return array
     */
    public function getModuleConfigurationFiles($filename)
    {
       return $this->_storage->getModuleConfigurationFiles($filename);
    }

    /**
     * Get default server variables values
     *
     * @return array
     */
    public function getDistroBaseUrl()
    {
        if (isset($_SERVER['SCRIPT_NAME']) && isset($_SERVER['HTTP_HOST'])) {
            $secure = (!empty($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] != 'off'))
                || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443');
            $scheme = ($secure ? 'https' : 'http') . '://' ;

            $hostArr = explode(':', $_SERVER['HTTP_HOST']);
            $host = $hostArr[0];
            $port = isset($hostArr[1]) && (!$secure && $hostArr[1] != 80 || $secure && $hostArr[1] != 443)
                ? ':'. $hostArr[1]
                : '';
            $path = Mage::app()->getRequest()->getBasePath();

            return $scheme . $host . $port . rtrim($path, '/') . '/';
        }
        return 'http://localhost/';
    }

    /**
     * Get module config node
     *
     * @param string $moduleName
     * @return Varien_Simplexml_Object
     */
    function getModuleConfig($moduleName='')
    {
        $modules = $this->getNode('modules');
        if (''===$moduleName) {
            return $modules;
        } else {
            return $modules->$moduleName;
        }
    }

    /**
     * Get module directory by directory type
     *
     * @param   string $type
     * @param   string $moduleName
     * @return  string
     */
    public function getModuleDir($type, $moduleName)
    {
        if (isset($this->_moduleDirs[$moduleName][$type])) {
            return $this->_moduleDirs[$moduleName][$type];
        }

        $codePool = (string)$this->getModuleConfig($moduleName)->codePool;
        /** @var $dirs Mage_Core_Model_Dir */
        $dirs = $this->_objectManager->get('Mage_Core_Model_Dir');
        $dir = $dirs->getDir(Mage_Core_Model_Dir::MODULES) . DS . $codePool . DS . uc_words($moduleName, DS);

        switch ($type) {
            case 'etc':
            case 'controllers':
            case 'sql':
            case 'data':
            case 'locale':
            case 'view':
                $dir .= DS . $type;
                break;
        }

        $dir = str_replace('/', DS, $dir);
        return $dir;
    }

    /**
     * Set path to the corresponding module directory
     *
     * @param string $moduleName
     * @param string $type directory type (etc, controllers, locale etc)
     * @param string $path
     * @return Mage_Core_Model_Config
     */
    public function setModuleDir($moduleName, $type, $path)
    {
        if (!isset($this->_moduleDirs[$moduleName])) {
            $this->_moduleDirs[$moduleName] = array();
        }
        $this->_moduleDirs[$moduleName][$type] = $path;
        return $this;
    }

    /**
     * Get standard path variables.
     *
     * To be used in blocks, templates, etc.
     *
     * @param array|string $args Module name if string
     * @return array
     */
    public function getPathVars($args=null)
    {
        $path = array();

        $path['baseUrl'] = Mage::getBaseUrl();
        $path['baseSecureUrl'] = Mage::getBaseUrl('link', true);

        return $path;
    }

    /**
     * Check rewrite section and apply rewrites to $className, if any
     *
     * @param   string $className
     * @return  string
     */
    protected function _applyClassRewrites($className)
    {
        if (!isset($this->_classNameCache[$className])) {
            if (isset($this->_xml->global->rewrites->$className)) {
                $className = (string) $this->_xml->global->rewrites->$className;
            }
            $this->_classNameCache[$className] = $className;
        }

        return $this->_classNameCache[$className];
    }

    /**
     * Retrieve block class name
     *
     * @param   string $blockClass
     * @return  string
     */
    public function getBlockClassName($blockClass)
    {
        return $this->getModelClassName($blockClass);
    }

    /**
     * Retrieve helper class name
     *
     * @param   string $helperClass
     * @return  string
     */
    public function getHelperClassName($helperClass)
    {
        return $this->getModelClassName($helperClass);
    }

    /**
     * Retrieve module class name
     *
     * @param   string $modelClass
     * @return  string
     */
    public function getModelClassName($modelClass)
    {
        return $this->_applyClassRewrites($modelClass);
    }

    /**
     * Get model class instance.
     *
     * Example:
     * $config->getModelInstance('catalog/product')
     *
     * Will instantiate Mage_Catalog_Model_Resource_Product
     *
     * @param string $modelClass
     * @param array|object $constructArguments
     * @return Mage_Core_Model_Abstract|false
     */
    public function getModelInstance($modelClass='', $constructArguments=array())
    {
        $className = $this->getModelClassName($modelClass);
        if (class_exists($className)) {
            Magento_Profiler::start('FACTORY:' . $className);
            $obj = $this->_objectManager->create($className, $constructArguments);
            Magento_Profiler::stop('FACTORY:' . $className);
            return $obj;
        } else {
            return false;
        }
    }

    /**
     * Get resource model object by alias
     *
     * @param   string $modelClass
     * @param   array $constructArguments
     * @return  object
     */
    public function getResourceModelInstance($modelClass='', $constructArguments=array())
    {
        return $this->getModelInstance($modelClass, $constructArguments);
    }

    /**
     * Get resource configuration for resource name
     *
     * @param string $name
     * @return Varien_Simplexml_Object
     */
    public function getResourceConfig($name)
    {
        return $this->_storage->getResourceConfig($name);
    }

    /**
     * Get connection configuration
     *
     * @param   string $name
     * @return  Varien_Simplexml_Element
     */
    public function getResourceConnectionConfig($name)
    {
        return $this->_storage->getResourceConnectionConfig($name);
    }

    /**
     * Retrieve resource type configuration for resource name
     *
     * @param string $type
     * @return Varien_Simplexml_Object
     */
    public function getResourceTypeConfig($type)
    {
        return $this->_xml->global->resource->connection->types->{$type};
    }

    /**
     * Retrieve store Ids for $path with checking
     *
     * if empty $allowValues then retrieve all stores values
     *
     * return array($storeId=>$pathValue)
     *
     * @param   string $path
     * @param   array  $allowValues
     * @return  array
     */
    public function getStoresConfigByPath($path, $allowValues = array(), $useAsKey = 'id')
    {
        $storeValues = array();
        $stores = $this->getNode('stores');
        foreach ($stores->children() as $code => $store) {
            switch ($useAsKey) {
                case 'id':
                    $key = (int) $store->descend('system/store/id');
                    break;

                case 'code':
                    $key = $code;
                    break;

                case 'name':
                    $key = (string) $store->descend('system/store/name');
            }
            if ($key === false) {
                continue;
            }

            $pathValue = (string) $store->descend($path);

            if (empty($allowValues)) {
                $storeValues[$key] = $pathValue;
            } else if (in_array($pathValue, $allowValues)) {
                $storeValues[$key] = $pathValue;
            }
        }
        return $storeValues;
    }

    /**
     * Check whether given path should be secure according to configuration security requirements for URL
     * "Secure" should not be confused with https protocol, it is about web/secure/*_url settings usage only
     *
     * @param string $url
     * @return bool
     */
    public function shouldUrlBeSecure($url)
    {
        if (!Mage::getStoreConfigFlag(Mage_Core_Model_Store::XML_PATH_SECURE_IN_FRONTEND)) {
            return false;
        }

        if (!isset($this->_secureUrlCache[$url])) {
            $this->_secureUrlCache[$url] = false;
            $secureUrls = $this->getNode('frontend/secure_url');
            foreach ($secureUrls->children() as $match) {
                if (strpos($url, (string)$match) === 0) {
                    $this->_secureUrlCache[$url] = true;
                    break;
                }
            }
        }

        return $this->_secureUrlCache[$url];
    }

    /**
     * Get DB table names prefix
     *
     * @return string
     */
    public function getTablePrefix()
    {
        return $this->_xml->global->resources->db->table_prefix;
    }

    /**
     * Get events configuration
     *
     * @param   string $area event area
     * @param   string $eventName event name
     * @return  Mage_Core_Model_Config_Element
     */
    public function getEventConfig($area, $eventName)
    {
        //return $this->getNode($area)->events->{$eventName};
        if (!isset($this->_eventAreas[$area])) {
            $this->_eventAreas[$area] = $this->getNode($area)->events;
        }
        return $this->_eventAreas[$area]->{$eventName};
    }

    /**
     * Save config value to DB
     *
     * @param string $path
     * @param string $value
     * @param string $scope
     * @param int $scopeId
     * @return Mage_Core_Store_Config
     */
    public function saveConfig($path, $value, $scope = 'default', $scopeId = 0)
    {
        $this->_storage->saveConfig($path, $value, $scope, $scopeId);
        return $this;
    }

    /**
     * Delete config value from DB
     *
     * @param   string $path
     * @param   string $scope
     * @param   int $scopeId
     * @return  Mage_Core_Model_Config
     */
    public function deleteConfig($path, $scope = 'default', $scopeId = 0)
    {
        $this->_storage->deleteConfig($path, $scope, $scopeId);

        return $this;
    }

    /**
     * Get fieldset from configuration
     *
     * @param string $name fieldset name
     * @param string $root fieldset area, could be 'admin'
     * @return null|array
     */
    public function getFieldset($name, $root = 'global')
    {
        /** @var $config Mage_Core_Model_Config_Base */
        $config = Mage::getSingleton('Mage_Core_Model_Config_Fieldset');
        $rootNode = $config->getNode($root . '/fieldsets');
        if (!$rootNode) {
            return null;
        }
        return $rootNode->$name ? $rootNode->$name->children() : null;
    }

    /**
     * Retrieve resource connection model name
     *
     * @param string $moduleName
     * @return string
     */
    public function getResourceConnectionModel($moduleName = null)
    {
       return $this->_storage->getResourceConnectionModel($moduleName);
    }

    /**
     * Get a resource model class name
     *
     * @param string $modelClass
     * @return string|false
     */
    public function getResourceModelClassName($modelClass)
    {
        return $this->getModelClassName($modelClass);
    }

    /**
     *  Get allowed areas
     *
     * @return array
     */
    public function getAreas()
    {
        if (is_null($this->_allowedAreas) ) {
            $this->_loadAreas();
        }

        return $this->_allowedAreas;
    }

    /**
     * Retrieve area config by area code
     *
     * @param string|null $areaCode
     * @return array
     */
    public function getAreaConfig($areaCode = null)
    {
        $areaCode = empty($areaCode) ? $this->getCurrentAreaCode() : $areaCode;
        $areas = $this->getAreas();
        if (!isset($areas[$areaCode])) {
            throw new InvalidArgumentException('Requested area (' . $areaCode . ') doesn\'t exist');
        }
        return $areas[$areaCode];
    }

    /**
     * Load allowed areas from config
     *
     * @return Mage_Core_Model_Config
     */
    protected function _loadAreas()
    {
        $this->_allowedAreas = array();
        $nodeAreas = $this->getNode('global/areas');
        if (is_object($nodeAreas)) {
            foreach ($nodeAreas->asArray() as $areaCode => $areaInfo) {
                if (empty($areaCode)
                    || (!isset($areaInfo['base_controller']) || empty($areaInfo['base_controller']))
                ) {
                    continue;
                }
                /**
                 * TODO: Check of 'routers' nodes existance is excessive:
                 * TODO: 'routers' check is moved Mage_Core_Model_Config::getRouters()
                 */

                /**
                 * TODO: Routers are not required in API.
                 * TODO: That is why Check for empty router class moved to Mage_Core_Model_Config::getRouters()
                 */
                $this->_allowedAreas[$areaCode] = $areaInfo;
            }
        }

        return $this;
    }

    /**
     * Get routers from config
     *
     * @return array
     */
    public function getRouters()
    {
        $routers = array();
        foreach ($this->getAreas() as $areaCode => $areaInfo) {
            if (isset($areaInfo['routers']) && is_array($areaInfo['routers'])) {
                foreach ($areaInfo['routers'] as $routerKey => $routerInfo ) {
                    if (!isset($routerInfo['class']) || empty($routerInfo['class'])) {
                        continue;
                    }
                    $routerInfo = array_merge($routerInfo, $areaInfo);
                    unset($routerInfo['routers']);
                    $routerInfo['area'] = $areaCode;
                    $routers[$routerKey] = $routerInfo;
                }
            }
        }
        return $routers;
    }

    public function isModuleEnabled($moduleName)
    {
        if (!$this->getNode('modules/' . $moduleName)) {
            return false;
        }

        $isActive = $this->getNode('modules/' . $moduleName . '/active');
        if (!$isActive || !in_array((string)$isActive, array('true', '1'))) {
            return false;
        }
        return true;
    }

    /**
     * Get currently used area code
     * @return string|null
     */
    public function getCurrentAreaCode()
    {
        return $this->_currentAreaCode;
    }

    /**
     * Set currently used area code
     *
     * @param $areaCode
     * @return Mage_Core_Model_Config
     */
    public function setCurrentAreaCode($areaCode)
    {
        $this->_currentAreaCode = $areaCode;
        return $this;
    }

    /**
     * Cleanup circular references
     *
     * Destructor should be called explicitly in order to work around the PHP bug
     * https://bugs.php.net/bug.php?id=62468
     */
    public function __destruct()
    {
        $this->_prototype = null;
        parent::__destruct();
    }
}
