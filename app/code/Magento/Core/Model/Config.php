<?php
/**
 * Application configuration object. Used to access configuration when application is initialized and installed.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Magento_Core_Model_Config implements Magento_Core_Model_ConfigInterface
{
    /**
     * Config cache tag
     */
    const CACHE_TAG = 'CONFIG';

    /**
     * Default configuration scope
     */
    const SCOPE_DEFAULT = 'default';

    /**
     * Stores configuration scope
     */
    const SCOPE_STORES = 'stores';

    /**
     * Websites configuration scope
     */
    const SCOPE_WEBSITES = 'websites';

    /**
     * Storage of validated secure urls
     *
     * @var array
     */
    protected $_secureUrlCache = array();

    /**
     * Active modules array per namespace
     *
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
     * Object manager
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Configuration storage
     *
     * @var Magento_Core_Model_Config_StorageInterface
     */
    protected $_storage;

    /**
     * Configuration data container
     *
     * @var Magento_Core_Model_ConfigInterface
     */
    protected $_config;

    /**
     * Module configuration reader
     *
     * @var Magento_Core_Model_Config_Modules_Reader
     */
    protected $_moduleReader;

    /**
     * @var Magento_Config_ScopeInterface
     */
    protected $_configScope;

    /**
     * @var Magento_Core_Model_ModuleListInterface
     */
    protected $_moduleList;

    /**
     * @var Magento_Core_Model_Config_SectionPool
     */
    protected $_sectionPool;

    /**
     * @var Magento_Core_Model_Resource_Store_Collection
     */
    protected $_storeCollection;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @param Magento_Core_Model_ObjectManager $objectManager
     * @param Magento_Core_Model_Config_StorageInterface $storage
     * @param Magento_Core_Model_Config_Modules_Reader $moduleReader
     * @param Magento_Core_Model_ModuleListInterface $moduleList
     * @param Magento_Config_ScopeInterface $configScope
     * @param Magento_Core_Model_Config_SectionPool $sectionPool
     * @param Magento_Config_ScopeInterface $configScope
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     */
    public function __construct(
        Magento_Core_Model_ObjectManager $objectManager,
        Magento_Core_Model_Config_StorageInterface $storage,
        Magento_Core_Model_Config_Modules_Reader $moduleReader,
        Magento_Core_Model_ModuleListInterface $moduleList,
        Magento_Config_ScopeInterface $configScope,
        Magento_Core_Model_Config_SectionPool $sectionPool,
        Magento_Core_Model_Store_Config $coreStoreConfig
    ) {
        Magento_Profiler::start('config_load');
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_objectManager = $objectManager;
        $this->_storage = $storage;
        $this->_config = $this->_storage->getConfiguration();
        $this->_moduleReader = $moduleReader;
        $this->_moduleList = $moduleList;
        $this->_configScope = $configScope;
        $this->_sectionPool = $sectionPool;
        Magento_Profiler::stop('config_load');
    }

    /**
     * Load allowed areas from config
     *
     * @return Magento_Core_Model_Config
     */
    protected function _loadAreas()
    {
        $this->_allowedAreas = array();
        $nodeAreas = $this->getNode('global/areas');
        if (is_object($nodeAreas)) {
            foreach ($nodeAreas->asArray() as $areaCode => $areaInfo) {
                if (empty($areaCode)) {
                    continue;
                }
                $this->_allowedAreas[$areaCode] = $areaInfo;
            }
        }

        return $this;
    }

    /**
     * Returns node found by the $path and scope info
     *
     * @param   string $path
     * @return Magento_Core_Model_Config_Element
     * @deprecated
     */
    public function getNode($path = null)
    {
        return $this->_config->getNode($path);
    }

    /**
     * Retrieve config value by path and scope
     *
     * @param string $path
     * @param string $scope
     * @param string $scopeCode
     * @return mixed
     */
    public function getValue($path = null, $scope = 'default', $scopeCode = null)
    {
        return $this->_sectionPool->getSection($scope, $scopeCode)->getValue($path);
    }

    /**
     * Set config value in the corresponding config scope
     *
     * @param string $path
     * @param mixed $value
     * @param string $scope
     * @param null|string $scopeCode
     */
    public function setValue($path, $value, $scope = 'default', $scopeCode = null)
    {
        $this->_sectionPool->getSection($scope, $scopeCode)->setValue($path, $value);
    }

    /**
     * Create node by $path and set its value.
     *
     * @param string $path separated by slashes
     * @param string $value
     * @param bool $overwrite
     */
    public function setNode($path, $value, $overwrite = true)
    {
        $this->_config->setNode($path, $value, $overwrite);
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
        $areaCode = empty($areaCode) ? $this->_configScope->getCurrentScope() : $areaCode;
        $areas = $this->getAreas();
        if (!isset($areas[$areaCode])) {
            throw new InvalidArgumentException('Requested area (' . $areaCode . ') does not exist');
        }
        return $areas[$areaCode];
    }

    /**
     * Identify front name of the requested area. Return current area front name if area code is not specified.
     *
     * @param string|null $areaCode
     * @return string
     * @throws LogicException If front name is not defined.
     */
    public function getAreaFrontName($areaCode = null)
    {
        $areaCode = empty($areaCode) ? $this->_configScope->getCurrentScope() : $areaCode;
        $areaConfig = $this->getAreaConfig($areaCode);
        if (!isset($areaConfig['frontName'])) {
            throw new LogicException(sprintf(
                'Area "%s" must have front name defined in the application config.',
                $areaCode
            ));
        }
        return $areaConfig['frontName'];
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
        return $this->_moduleReader->getModuleDir($type, $moduleName);
    }

    /**
     * Set path to the corresponding module directory
     *
     * @param string $moduleName
     * @param string $type directory type (etc, controllers, locale etc)
     * @param string $path
     * @return Magento_Core_Model_Config
     */
    public function setModuleDir($moduleName, $type, $path)
    {
        $this->_moduleReader->setModuleDir($moduleName, $type, $path);
        return $this;
    }

    /**
     * Retrieve store Ids for $path with checking
     *
     * if empty $allowValues then retrieve all stores values
     *
     * return array($storeId => $pathValue)
     *
     * @param string $path
     * @param array $allowedValues
     * @param string $keyAttribute
     * @return array
     * @throws InvalidArgumentException
     */
    public function getStoresConfigByPath($path, $allowedValues = array(), $keyAttribute = 'id')
    {
        // @todo inject custom store collection that corresponds to the following requirements
        if (is_null($this->_storeCollection)) {
            $this->_storeCollection = $this->_objectManager->create('Magento_Core_Model_Resource_Store_Collection');
            $this->_storeCollection->setLoadDefault(true);
        }
        $storeValues = array();
        /** @var $store Magento_Core_Model_Store */
        foreach ($this->_storeCollection as $store) {
            switch ($keyAttribute) {
                case 'id':
                    $key = $store->getId();
                    break;
                case 'code':
                    $key = $store->getCode();
                    break;
                case 'name':
                    $key = $store->getName();
                    break;
                default:
                    throw new InvalidArgumentException("'{$keyAttribute}' cannot be used as a key.");
                    break;
            }

            $value = $this->getValue($path, 'store', $store->getCode());
            if (empty($allowedValues)) {
                $storeValues[$key] = $value;
            } elseif (in_array($value, $allowedValues)) {
                $storeValues[$key] = $value;
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
        if (!$this->_coreStoreConfig->getConfigFlag(Magento_Core_Model_Store::XML_PATH_SECURE_IN_FRONTEND)) {
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
            foreach ($this->_moduleList->getModules() as $module) {
                $moduleName = $module['name'];
                $module = strtolower($moduleName);
                $this->_moduleNamespaces[substr($module, 0, strpos($module, '_'))][$module] = $moduleName;
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
            if (isset($name[1])) {
                $fullNS = $name[0] . '_' . $name[1];
                if (2 <= $partsNum && isset($namespace[$fullNS])) {
                    return $asFullModuleName ? $namespace[$fullNS] : $fullNS;
                }
            }
        }
        return '';
    }

    /**
     * Reinitialize configuration
     *
     * @return Magento_Core_Model_Config
     */
    public function reinit()
    {
        $this->_sectionPool->clean();
    }

    /**
     * Remove configuration cache
     */
    public function removeCache()
    {
        $this->_storage->removeCache();
    }

    /**
     * Reload xml configuration data
     * @deprecated must be removed after Installation logic is removed from application
     */
    public function reloadConfig()
    {
        $this->_storage->removeCache();
        $this->_config = $this->_storage->getConfiguration();
    }
}
