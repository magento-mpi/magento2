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
    /**
     * Config cache tag
     */
    const CACHE_TAG = 'CONFIG';

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
     * Recursion level provide availability cache sub-nodes separately
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
     * Storage of validated secure urls
     *
     * @var array
     */
    protected $_secureUrlCache = array();

    /**
     * Configuration for events by area
     *
     * @var array
     */
    protected $_eventAreas;

    /**
     * Active modules array per namespace
     * @var array
     */
    private $_moduleNamespaces = null;

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
     * @var Mage_Core_Model_Config_StorageInterface
     */
    protected $_storage;

    /**
     * @var Mage_Core_Model_Config_Base
     */
    protected $_data;

    /**
     * @var Mage_Core_Model_Dir
     */
    protected $_dirs;

    /**
     * @var Mage_Core_Model_AppInterface
     */
    protected $_app;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Model_Dir $dirs
     * @param Mage_Core_Model_Config_StorageInterface $configStorage
     * @param Mage_Core_Model_Config_Base_Factory $configFactory
     * @param Mage_Core_Model_AppInterface $app
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Mage_Core_Model_Dir $dirs,
        Mage_Core_Model_Config_StorageInterface $configStorage,
        Mage_Core_Model_Config_Base_Factory $configFactory,
        Mage_Core_Model_AppInterface $app
    ) {
        $this->_objectManager = $objectManager;
        $this->_app = $app;
        $this->_cacheChecksum = null;
        $this->_dirs = $dirs;
        $this->_data = $configFactory->create($this->_storage->getConfiguration());
        $this->_loadInstallDate();
    }

    /**
     * Load application installation date
     */
    protected function _loadInstallDate()
    {
        $installDateNode = $this->getNode(Mage_Core_Model_App::XML_PATH_INSTALL_DATE);
        if ($installDateNode) {
            $this->_installDate = strtotime((string)$installDateNode);
        }
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
     * Check rewrite section and apply rewrites to $className, if any
     *
     * @param   string $className
     * @return  string
     */
    protected function _applyClassRewrites($className)
    {
        if (!isset($this->_classNameCache[$className])) {
            $rewrites = (string) $this->getNode('global/rewrites/' . $className);
            if (!empty($rewrites)) {
                $className = $rewrites;
            }
            $this->_classNameCache[$className] = $className;
        }

        return $this->_classNameCache[$className];
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
     * Returns nodes found by xpath expression
     *
     * @param string $xpath
     * @return array
     */
    public function getXpath($xpath)
    {
        return $this->_data->getXpath($xpath);
    }

    /**
     * Returns node found by the $path and scope info
     *
     * @param   string $path
     * @param   string $scope
     * @param   string|int $scopeCode
     * @return Mage_Core_Model_Config_Element
     */
    public function getNode($path = null, $scope = '', $scopeCode = null)
    {
        if ($scope !== '') {
            if (('store' === $scope) || ('website' === $scope)) {
                $scope .= 's';
            }
            if (('default' !== $scope) && is_int($scopeCode)) {
                if ('stores' == $scope) {
                    $scopeCode = $this->_app->getStore($scopeCode)->getCode();
                } elseif ('websites' == $scope) {
                    $scopeCode = $this->_app->getWebsite($scopeCode)->getCode();
                } else {
                    Mage::throwException(
                        $this->_objectManager->get('Mage_Core_Helper_Data')
                            ->__('Unknown scope "%s".', $scope)
                    );
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

        return $this->_data->getNode($path);
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
        return $this->_data->setNode($path, $value, $overwrite);
    }

    /**
     * Get node value from cached section data
     *
     * @param   array $path
     * @return  Mage_Core_Model_Config_Element|bool
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
     * Get allowed areas
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
     * @throws InvalidArgumentException
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

    /**
     * Get module config node
     *
     * @param string $moduleName
     * @return Varien_Simplexml_Element
     */
    function getModuleConfig($moduleName = '')
    {
        $modules = $this->getNode('modules');
        if ('' === $moduleName) {
            return $modules;
        } else {
            return $modules->$moduleName;
        }
    }

    /**
     * Check if specified module is enabled
     *
     * @param string $moduleName
     * @return bool
     */
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

        $dir = $this->_dirs->getDir(Mage_Core_Model_Dir::MODULES) . DIRECTORY_SEPARATOR
            . $codePool . DIRECTORY_SEPARATOR
            . uc_words($moduleName, DIRECTORY_SEPARATOR);

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
     * Retrieve store Ids for $path with checking
     *
     * if empty $allowValues then retrieve all stores values
     *
     * return array($storeId => $pathValue)
     *
     * @param   string $path
     * @param   array  $allowValues
     * @param   string  $useAsKey
     * @return  array
     */
    public function getStoresConfigByPath($path, $allowValues = array(), $useAsKey = 'id')
    {
        $storeValues = array();
        $stores = $this->getNode('stores');
        /** @var $store Varien_Simplexml_Element */
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
                    break;

                default:
                    $key = false;
                    break;
            }

            if ($key === false) {
                continue;
            }

            $pathValue = (string) $store->descend($path);

            if (empty($allowValues)) {
                $storeValues[$key] = $pathValue;
            } elseif (in_array($pathValue, $allowValues)) {
                $storeValues[$key] = $pathValue;
            }
        }

        return $storeValues;
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
        $config = $this->_objectManager->get('Mage_Core_Model_Config_Fieldset');
        $rootNode = $config->getNode($root . '/fieldsets');
        if (!$rootNode) {
            return null;
        }
        return $rootNode->$name ? $rootNode->$name->children() : null;
    }

    /**
     * Get standard path variables.
     *
     * To be used in blocks, templates, etc.
     *
     * @param array|string $args Module name if string
     * @return array
     */
    public function getPathVars($args = null)
    {
        $path = array();
        $path['baseUrl'] = Mage::getBaseUrl();
        $path['baseSecureUrl'] = Mage::getBaseUrl('link', true);
        return $path;
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
            $path = $this->_app->getRequest()->getBasePath();

            return $scheme . $host . $port . rtrim($path, '/') . '/';
        }
        return 'http://localhost/';
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
     * Get DB table names prefix
     *
     * @return string
     */
    public function getTablePrefix()
    {
        return $this->getNode('global/resources/db/table_prefix');
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
        if (!isset($this->_eventAreas[$area])) {
            $this->_eventAreas[$area] = $this->getNode($area)->events;
        }
        return $this->_eventAreas[$area]->{$eventName};
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
            /** @var $modelConfig Varien_Simplexml_Element */
            foreach ($this->getXpath('modules/*') as $modelConfig) {
                if ((string)$modelConfig->active == 'true') {
                    $moduleName = $modelConfig->getName();
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
     * specified xml file name to one object
     *
     * @param $fileName
     * @param null $mergeToObject
     * @param null $mergeModel
     * @return Mage_Core_Model_Config_Base
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
     * Reinitialize configuration
     *
     * @return Mage_Core_Model_Config
     */
    public function reinit()
    {
        $this->_data = $this->_storage->getConfiguration(false);
    }

    /**
     * Get resource configuration for resource name
     *
     * @param string $name
     * @return Varien_Simplexml_Element
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
     * Get model class instance.
     *
     * Example:
     * $config->getModelInstance('catalog/product')
     *
     * Will instantiate Mage_Catalog_Model_Resource_Product
     *
     * @param string $modelClass
     * @param array|object $constructArguments
     * @return Mage_Core_Model_Abstract|bool
     */
    public function getModelInstance($modelClass = '', $constructArguments = array())
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
     * Retrieve resource type configuration for resource name
     *
     * @param string $type
     * @return Varien_Simplexml_Element
     */
    public function getResourceTypeConfig($type)
    {
        return $this->getNode('global/resource/connection/types/' . $type);
    }

    /**
     * Get a resource model class name
     *
     * @param string $modelClass
     * @return string
     */
    public function getResourceModelClassName($modelClass)
    {
        return $this->getModelClassName($modelClass);
    }

    /**
     * Remove configuration cache
     */
    public function removeCache()
    {
        $tags = array(self::CACHE_TAG);
        Mage::dispatchEvent('application_clean_cache', array('tags' => $tags));
        $this->_storage->removeCache($tags);
        $this->_data->removeCache();
    }
}
