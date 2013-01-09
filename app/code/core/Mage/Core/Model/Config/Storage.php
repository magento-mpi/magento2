<?php

class Mage_Core_Model_Config_Storage
{
    /**
     * Config cache id
     *
     * @var string
     */
    protected $_cacheId;

    /**
     * @var Mage_Core_Model_CacheInterface
     */
    protected $_cache;

    /**
     * @var Mage_Core_Model_Dir
     */
    protected $_dirs;

    /**
     * @var Mage_Core_Model_Config_Base
     */
    protected $_container;

    /**
     * @var Mage_Core_Model_Config_Base
     */
    protected $_prototype;

    /**
     * Whether local configuration is loaded or not
     *
     * @var bool
     */
    protected $_isLocalConfigLoaded = false;

    public function __construct(Mage_Core_Model_Dir $dirs,
        Mage_Core_Model_CacheInterface $cache,
        Mage_Core_Model_Config_Base $container,
        Mage_Core_Model_Config_Base $prototype,
        $extraFile,
        $extraData
    ) {
        $this->_cacheId = 'config_global';
        $this->_cache = $cache;
        $this->_dirs = $dirs;
        $this->_prototype = $prototype;
        $this->_container = $container;

        //$this->setCacheChecksum(null);
        //$this->_cacheLoadedSections = array();
        $this->loadBase();

        $cacheLoad = $this->loadModulesCache();
        if ($cacheLoad) {
            return $this;
        }
        $this->loadModules();
        $this->loadDb();
        $this->loadLocales();
        $this->saveCache();
    }

    public function reload()
    {

    }

    /**
     * Load base configuration
     *
     * @return Mage_Core_Model_Config
     */
    public function loadBase()
    {
        $etcDir = $this->_dirs->getDir(Mage_Core_Model_Dir::CONFIG);
        if (!$this->_container->getNode()) {
            $this->_container->loadString('<config/>');
        }
        // 1. app/etc/*.xml (except local config)
        foreach (scandir($etcDir) as $filename) {
            if ('.' == $filename || '..' == $filename || '.xml' != substr($filename, -4)
                || Mage_Core_Model_Config::LOCAL_CONFIG_FILE == $filename
            ) {
                continue;
            }
            $baseConfigFile = $etcDir . DIRECTORY_SEPARATOR . $filename;
            $baseConfig = clone $this->_prototype;
            $baseConfig->loadFile($baseConfigFile);
            $this->_container->extend($baseConfig);
        }
        // 2. local configuration
        $this->_loadLocalConfig();
    }

    /**
     * Load local configuration (part of the base configuration)
     */
    protected function _loadLocalConfig()
    {
        $etcDir = $this->_dirs->getDir(Mage_Core_Model_Dir::CONFIG);
        $localConfigParts = array();

        $localConfigFile = $etcDir . DIRECTORY_SEPARATOR . Mage_Core_Model_Config::LOCAL_CONFIG_FILE;
        if (file_exists($localConfigFile)) {
            // 1. app/etc/local.xml
            $localConfig = clone $this->_prototype;
            $localConfig->loadFile($localConfigFile);
            $localConfigParts[] = $localConfig;

            // 2. app/etc/<dir>/<file>.xml
            $localConfigExtraFile = $this->_extraFile;
            if (preg_match('/^[a-z\d_-]+\/[a-z\d_-]+\.xml$/', $localConfigExtraFile)) {
                $localConfigExtraFile = $etcDir . DIRECTORY_SEPARATOR . $localConfigExtraFile;
                $localConfig = clone $this->_prototype;
                $localConfig->loadFile($localConfigExtraFile);
                $localConfigParts[] = $localConfig;
            }
        }

        // 3. extra local configuration string
        $localConfigExtraData = $this->_extraData;
        if ($localConfigExtraData) {
            $localConfig = clone $this->_prototype;
            $localConfig->loadString($localConfigExtraData);
            $localConfigParts[] = $localConfig;
        }

        if ($localConfigParts) {
            foreach ($localConfigParts as $oneConfigPart) {
                $this->_container->extend($oneConfigPart);
            }
            $this->_isLocalConfigLoaded = true;
        }
    }

    /**
     * Load locale configuration from locale configuration files
     *
     * @return Mage_Core_Model_Config
     */
    public function loadLocales()
    {
        $localeDir = $this->_dirs->getDir(Mage_Core_Model_Dir::LOCALE);
        $files = glob($localeDir . DS . '*' . DS . 'config.xml');

        if (is_array($files) && !empty($files)) {
            foreach ($files as $file) {
                $merge = clone $this->_prototype;
                $merge->loadFile($file);
                $this->_container->extend($merge);
            }
        }
        return $this;
    }

    /**
     * Load cached modules and locale configuration
     *
     * @return bool
     */
    public function loadModulesCache()
    {
        if ($this->getInstallDate()) {
            if ($this->_canUseCacheForInit()) {
                Magento_Profiler::start('init_modules_config_cache');
                $loaded = $this->_container->loadCache();
                Magento_Profiler::stop('init_modules_config_cache');
                if ($loaded) {
                    $this->_useCache = true;
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check if cache can be used for config initialization
     *
     * @return bool
     */
    protected function _canUseCacheForInit()
    {
        return  $this->_cacheModel->canUse('config') && $this->_allowCacheForInit
            && !$this->_loadCache($this->_getCacheLockId());
    }

    /**
     * Load modules configuration
     *
     * @return Mage_Core_Model_Config
     */
    public function loadModules()
    {
        Magento_Profiler::start('config');
        Magento_Profiler::start('load_modules');
        $this->_loadDeclaredModules();

        Magento_Profiler::start('load_modules_configuration');
        $resourceConfig = sprintf('config.%s.xml', $this->getResourceConnectionModel('core'));
        $this->loadModulesConfiguration(array('config.xml',$resourceConfig), $this);
        Magento_Profiler::stop('load_modules_configuration');

        // Prevent local configuration overriding
        $this->_loadLocalConfig();

        $this->_container->applyExtends();
        Magento_Profiler::stop('load_modules');
        Magento_Profiler::stop('config');
        return $this;
    }

    /**
     * Load declared modules configuration
     *
     * @return  Mage_Core_Model_Config
     */
    protected function _loadDeclaredModules()
    {
        Magento_Profiler::start('load_modules_files');
        $moduleFiles = $this->_getDeclaredModuleFiles();
        if (!$moduleFiles) {
            return $this;
        }
        Magento_Profiler::stop('load_modules_files');

        Magento_Profiler::start('load_modules_declaration');
        $unsortedConfig = new Mage_Core_Model_Config_Base('<config/>');
        $emptyConfig = new Mage_Core_Model_Config_Element('<config><modules/></config>');
        $declaredModules = array();
        foreach ($moduleFiles as $oneConfigFile) {
            $path = explode(DIRECTORY_SEPARATOR, $oneConfigFile);
            $moduleConfig = new Mage_Core_Model_Config_Base($oneConfigFile);
            $modules = $moduleConfig->getXpath('modules/*');
            if (!$modules) {
                continue;
            }
            $cPath = count($path);
            if ($cPath > 4) {
                $moduleName = $path[$cPath - 4] . '_' . $path[$cPath - 3];
                $this->_modulesCache[$moduleName] = $moduleConfig;
            }
            foreach ($modules as $module) {
                $moduleName = $module->getName();
                $isActive = (string)$module->active;
                if (isset($declaredModules[$moduleName])) {
                    $declaredModules[$moduleName]['active'] = $isActive;
                    continue;
                }
                $newModule = clone $emptyConfig;
                $newModule->modules->appendChild($module);
                $declaredModules[$moduleName] = array(
                    'active' => $isActive,
                    'module' => $newModule,
                );
            }
        }
        foreach ($declaredModules as $moduleName => $module) {
            if ($module['active'] == 'true') {
                $module['module']->modules->{$moduleName}->active = 'true';
                $unsortedConfig->extend(new Mage_Core_Model_Config_Base($module['module']));
            }
        }
        $sortedConfig = new Mage_Core_Model_Config_Module($unsortedConfig, $this->_allowedModules);

        $this->extend($sortedConfig);
        Magento_Profiler::stop('load_modules_declaration');
        return $this;
    }

    /**
     * Retrive Declared Module file list
     *
     * @return array
     */
    protected function _getDeclaredModuleFiles()
    {
        /** @var $dirs Mage_Core_Model_Dir */
        $dirs = $this->_objectManager->get('Mage_Core_Model_Dir');
        $codeDir = $dirs->getDir(Mage_Core_Model_Dir::MODULES);
        $moduleFiles = glob($codeDir . DS . '*' . DS . '*' . DS . '*' . DS . 'etc' . DS . 'config.xml');

        if (!$moduleFiles) {
            return false;
        }

        $collectModuleFiles = array(
            'base'   => array(),
            'mage'   => array(),
            'custom' => array()
        );

        foreach ($moduleFiles as $v) {
            $name = explode(DIRECTORY_SEPARATOR, $v);
            $collection = $name[count($name) - 4];

            if ($collection == 'Mage') {
                $collectModuleFiles['mage'][] = $v;
            } else {
                $collectModuleFiles['custom'][] = $v;
            }
        }

        $etcDir = $dirs->getDir(Mage_Core_Model_Dir::CONFIG);
        $additionalFiles = glob($etcDir . DS . 'modules' . DS . '*.xml');

        foreach ($additionalFiles as $v) {
            $collectModuleFiles['base'][] = $v;
        }

        return array_merge(
            $collectModuleFiles['mage'],
            $collectModuleFiles['custom'],
            $collectModuleFiles['base']
        );
    }

    /**
     * Load config data from DB
     *
     * @return Mage_Core_Model_Config
     */
    public function loadDb()
    {
        Magento_Profiler::start('config');
        if ($this->getInstallDate()) {
            Magento_Profiler::start('load_db');
            $dbConf = $this->getResourceModel();
            $dbConf->loadToXml($this);
            Magento_Profiler::stop('load_db');
        }
        Magento_Profiler::stop('config');
        return $this;
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
     * Save configuration cache
     *
     * @param   array $tags cache tags
     * @return  Mage_Core_Model_Config
     */
    public function saveCache($tags=array())
    {
        if (!$this->_cache->canUse('config')) {
            return $this;
        }
        if (!in_array(Mage_Core_Model_Config::CACHE_TAG, $tags)) {
            $tags[] = Mage_Core_Model_Config::CACHE_TAG;
        }
        $cacheLockId = $this->_getCacheLockId();
        if ($this->_loadCache($cacheLockId)) {
            return $this;
        }

        if (!empty($this->_cacheSections)) {
            $xml = clone $this->_xml;
            foreach ($this->_cacheSections as $sectionName => $level) {
                $this->_saveSectionCache($this->getCacheId(), $sectionName, $xml, $level, $tags);
                unset($xml->$sectionName);
            }
            $this->_cachePartsForSave[$this->getCacheId()] = $xml->asNiceXml('', false);
        } else {
            return parent::saveCache($tags);
        }

        $this->_saveCache(time(), $cacheLockId, array(), 60);
        $this->removeCache();
        foreach ($this->_cachePartsForSave as $cacheId => $cacheData) {
            $this->_saveCache($cacheData, $cacheId, $tags, $this->getCacheLifetime());
        }
        unset($this->_cachePartsForSave);
        $this->_removeCache($cacheLockId);
        return $this;
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
     * Get lock flag cache identifier
     *
     * @return string
     */
    protected function _getCacheLockId()
    {
        return $this->getCacheId().'.lock';
    }

    /**
     * Save cache of specified
     *
     * @param   string $idPrefix cache id prefix
     * @param   string $sectionName
     * @param   Varien_Simplexml_Element $source
     * @param   int $recursionLevel
     * @return  Mage_Core_Model_Config
     */
    protected function _saveSectionCache($idPrefix, $sectionName, $source, $recursionLevel=0, $tags=array())
    {
        if ($source && $source->$sectionName) {
            $cacheId = $idPrefix . '_' . $sectionName;
            if ($recursionLevel > 0) {
                foreach ($source->$sectionName->children() as $subSectionName => $node) {
                    $this->_saveSectionCache(
                        $cacheId, $subSectionName, $source->$sectionName, $recursionLevel-1, $tags
                    );
                }
            }
            $this->_cachePartsForSave[$cacheId] = $source->$sectionName->asNiceXml('', false);
        }
        return $this;
    }


    /**
     * Load config section cached data
     *
     * @param   string $sectionName
     * @return  Varien_Simplexml_Element
     */
    protected function _loadSectionCache($sectionName)
    {
        $cacheId = $this->getCacheId() . '_' . $sectionName;
        $xmlString = $this->_loadCache($cacheId);

        /**
         * If we can't load section cache (problems with cache storage)
         */
        if (!$xmlString) {
            $this->_useCache = false;
            $this->reinit();
            return false;
        } else {
            $xml = simplexml_load_string($xmlString, $this->_elementClass);
            return $xml;
        }
    }

    /**
     * Load cached data by identifier
     *
     * @param   string $id
     * @return  string
     */
    protected function _loadCache($id)
    {
        return $this->_cacheModel->load($id);
    }

    /**
     * Save cache data
     *
     * @param   string $data
     * @param   string $id
     * @param   array $tags
     * @param   bool|int $lifeTime
     * @return  Mage_Core_Model_Config
     */
    protected function _saveCache($data, $id, $tags = array(), $lifeTime = false)
    {
        $this->_cacheModel->save($data, $id, $tags, $lifeTime);
        return $this;
    }

    /**
     * Clear cache data by id
     *
     * @param   string $id
     * @return  Mage_Core_Model_Config
     */
    protected function _removeCache($id)
    {
        $this->_cacheModel->remove($id);
        return $this;
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

//    /**
//     * Retrieve cache object
//     *
//     * @return Mage_Core_Model_CacheInterface
//     */
//    public function getCache()
//    {
//        return $this->_cacheModel;
//    }

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
