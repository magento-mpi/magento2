<?php
/**
 * Application config storage
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_Storage implements Mage_Core_Model_Config_StorageInterface
{
    /**
     * Config cache id
     *
     * @var string
     */
    protected $_cacheId;

    /**
     * Flag which allow use cache logic
     *
     * @var bool
     */
    protected $_useCache = false;

    /**
     * Cache object
     *
     * @var Mage_Core_Model_CacheInterface
     */
    protected $_cache;

    /**
     * Directory registry
     *
     * @var Mage_Core_Model_Dir
     */
    protected $_dirs;

    /**
     * Xml container
     *
     * @var Mage_Core_Model_Config_Base
     */
    protected $_container;

    /**
     * XMl clonning model
     *
     * @var Mage_Core_Model_Config_Base
     */
    protected $_prototype;

    /**
     * List of modules to load
     *
     * @var array
     */
    protected $_allowedModules = array();

    /**
     * Loaded modules configuration
     *
     * @var array
     */
    protected $_modulesCache = array();

    /**
     * Whether local configuration is loaded or not
     *
     * @var bool
     */
    protected $_isLocalConfigLoaded = false;

    /**
     * @param Mage_Core_Model_Dir $dirs
     * @param Mage_Core_Model_Cache $cache
     * @param Mage_Core_Model_Config_Base $container
     * @param Mage_Core_Model_Config_Base $prototype
     * @param string $extraFile
     * @param string $extraData
     * @param array $allowedModules
     */
    public function __construct(
        Mage_Core_Model_Cache $cache,
        Mage_Core_Model_Config_Loader $loader
    ) {
        $this->_cacheId = 'config_global';
        $this->_cache = $cache;
    }

    /**
     * Retrieve application configuration
     *
     * @param bool $useCache
     * @return mixed|string
     */
    public function getConfiguration($useCache = true)
    {
        $config = $useCache ? $this->_cache->load($this->_cacheId) : '';
        if (!$config) {
            $config = $this->_loader->load()->asNiceXml(0, '');
            $config = $this->_container->getNode()->asXml();
            if ($useCache) {
                $this->_cache->save($config, $this->_cacheId, array(Mage_Core_Model_Config::CACHE_TAG));
            }
        }
        return $config;
    }

    /**
     * Remove configuration cache
     * @param array $tags
     */
    public function removeCache(array $tags)
    {
        // TODO: Implement removeCache() method.
    }

    /**
     * Get config resource model
     *
     * @return Mage_Core_Model_Resource_Config
     */
    public function getResourceModel()
    {
        if (is_null($this->_resourceModel)) {
            $this->_resourceModel = Mage::getResourceModel('Mage_Core_Model_Resource_Config');
        }
        return $this->_resourceModel;
    }

    /**
     * Retrieve resource connection model name
     *
     * @param string $moduleName
     * @return string
     */
    public function getResourceConnectionModel($moduleName = null)
    {
        $config = null;
        if (!is_null($moduleName)) {
            $setupResource = $moduleName . '_setup';
            $config        = $this->getResourceConnectionConfig($setupResource);
        }
        if (!$config) {
            $config = $this->getResourceConnectionConfig(Mage_Core_Model_Resource::DEFAULT_SETUP_RESOURCE);
        }

        return (string)$config->model;
    }

    public function getResourceConnectionConfig($name)
    {
        $config = $this->getResourceConfig($name);
        if ($config) {
            $conn = $config->connection;
            if ($conn) {
                if (!empty($conn->use)) {
                    return $this->getResourceConnectionConfig((string)$conn->use);
                } else {
                    return $conn;
                }
            }
        }
        return false;
    }

    /**
     * Get resource configuration for resource name
     *
     * @param string $name
     * @return Varien_Simplexml_Object
     */
    public function getResourceConfig($name)
    {
        return $this->_xml->global->resources->{$name};
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
        if ($mergeToObject === null) {
            $mergeToObject = clone $this->_prototype;
        }
        if ($mergeModel === null) {
            $mergeModel = clone $this->_prototype;
        }
        $modules = $this->getNode('modules')->children();
        foreach ($modules as $modName=>$module) {
            if ($module->is('active')) {
                if (!is_array($fileName)) {
                    $fileName = array($fileName);
                }
                foreach ($fileName as $configFile) {
                    if ($configFile == 'config.xml' && isset($this->_modulesCache[$modName])) {
                        $mergeToObject->extend($this->_modulesCache[$modName], true);
                        //Prevent overriding <active> node of module if it was redefined in etc/modules
                        $mergeToObject->extend(new Mage_Core_Model_Config_Base(
                                "<config><modules><{$modName}><active>true</active></{$modName}></modules></config>"),
                            true
                        );
                    } else {
                        $configFilePath = $this->getModuleDir('etc', $modName) . DS . $configFile;
                        if ($mergeModel->loadFile($configFilePath)) {
                            $mergeToObject->extend($mergeModel, true);
                        }
                    }
                }
            }
        }
        unset($this->_modulesCache);
        return $mergeToObject;
    }

    /**
     * Go through all modules and find configuration files of active modules
     *
     * @param string $filename
     * @return array
     */
    public function getModuleConfigurationFiles($filename)
    {
        $result = array();
        $modules = $this->getNode('modules')->children();
        foreach ($modules as $moduleName => $module) {
            if ((!$module->is('active'))) {
                continue;
            }
            $file = $this->getModuleDir('etc', $moduleName) . DIRECTORY_SEPARATOR . $filename;
            if (file_exists($file)) {
                $result[] = $file;
            }
        }
        return $result;
    }


    /**
     * Save config value to DB
     *
     * @param string $path
     * @param string $value
     * @param string $scope
     * @param int $scopeId
     * @return Mage_Core_Model_Config_Storage
     */
    public function saveConfig($path, $value, $scope = 'default', $scopeId = 0)
    {
        $resource = $this->getResourceModel();
        $resource->saveConfig(rtrim($path, '/'), $value, $scope, $scopeId);

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
        $resource = $this->getResourceModel();
        $resource->deleteConfig(rtrim($path, '/'), $scope, $scopeId);
        return $this;
    }
}
