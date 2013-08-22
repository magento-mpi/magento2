<?php
/**
 * Abstract config data reader.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class Magento_Webapi_Model_Config_ReaderAbstract
{
    /**
     * Cache ID for resource config.
     */
    const CONFIG_CACHE_ID = 'API-RESOURCE-CACHE';

    /**
     * Pattern for API action controllers class name.
     */
    const RESOURCE_CLASS_PATTERN = '/^(.*)_(.*)_Controller_Webapi(_.*)+$/';

    /**
     * @var Zend\Code\Scanner\DirectoryScanner
     */
    protected $_directoryScanner;

    /**
     * @var Magento_Webapi_Model_Config_Reader_ClassReflectorAbstract
     */
    protected $_classReflector;

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_applicationConfig;

    /**
     * @var Magento_Core_Model_CacheInterface
     */
    protected $_cache;

    /**
     * @var Magento_Core_Model_ModuleListInterface
     */
    protected $_moduleList;

    /**
     * @var array
     */
    protected $_data = array();

    /**
     * @var Magento_Core_Model_Cache_StateInterface
     */
    protected $_cacheState;

    /**
     * @param Magento_Webapi_Model_Config_Reader_ClassReflectorAbstract $classReflector
     * @param Magento_Core_Model_Config $appConfig
     * @param Magento_Core_Model_CacheInterface $cache
     * @param Magento_Core_Model_ModuleListInterface $moduleList
     * @param Magento_Core_Model_Cache_StateInterface $cacheState
     */
    public function __construct(
        Magento_Webapi_Model_Config_Reader_ClassReflectorAbstract $classReflector,
        Magento_Core_Model_Config $appConfig,
        Magento_Core_Model_CacheInterface $cache,
        Magento_Core_Model_ModuleListInterface $moduleList,
        Magento_Core_Model_Cache_StateInterface $cacheState
    ) {
        $this->_classReflector = $classReflector;
        $this->_applicationConfig = $appConfig;
        $this->_cache = $cache;
        $this->_moduleList = $moduleList;
        $this->_cacheState = $cacheState;
    }

    /**
     * Retrieve cache ID.
     *
     * @return string
     */
    abstract public function getCacheId();

    /**
     * Get current directory scanner. Initialize if it was not initialized previously.
     *
     * @return Zend\Code\Scanner\DirectoryScanner
     */
    public function getDirectoryScanner()
    {
        if (!$this->_directoryScanner) {
            $this->_directoryScanner = new Zend\Code\Scanner\DirectoryScanner();
            foreach ($this->_moduleList->getModules() as $module) {
                /** Invalid type is specified to retrieve path to module directory. */
                $moduleDir = $this->_applicationConfig->getModuleDir('invalid_type', $module['name']);
                $directory = $moduleDir . DS . 'Controller' . DS . 'Webapi';
                if (is_dir($directory)) {
                    $this->_directoryScanner->addDirectory($directory);
                }
            }
        }

        return $this->_directoryScanner;
    }

    /**
     * Set directory scanner object.
     *
     * @param Zend\Code\Scanner\DirectoryScanner $directoryScanner
     */
    public function setDirectoryScanner(Zend\Code\Scanner\DirectoryScanner $directoryScanner)
    {
        $this->_directoryScanner = $directoryScanner;
    }

    /**
     * Read configuration data from the action controllers files using class reflector.
     *
     * @throws InvalidArgumentException
     * @throws LogicException
     * @return array
     */
    public function getData()
    {
        if (!$this->_data && !$this->_loadDataFromCache()) {
            /** @var \Zend\Code\Scanner\FileScanner $file */
            foreach ($this->getDirectoryScanner()->getFiles(true) as $file) {
                $filename = $file->getFile();
                $classes = $file->getClasses();
                if (count($classes) > 1) {
                    throw new LogicException(sprintf(
                        'There can be only one class in the "%s" controller file .',
                        $filename
                    ));
                }
                /** @var \Zend\Code\Scanner\ClassScanner $class */
                $class = current($classes);
                $className = $class->getName();
                if (preg_match(self::RESOURCE_CLASS_PATTERN, $className)) {
                    $classData = $this->_classReflector->reflectClassMethods($className);
                    $this->_addData($classData);
                }
            }
            $postReflectionData = $this->_classReflector->getPostReflectionData();
            $this->_addData($postReflectionData);

            if (!isset($this->_data['resources'])) {
                throw new LogicException('Cannot populate config - no action controllers were found.');
            }

            $this->_saveDataToCache();
        }

        return $this->_data;
    }

    /**
     * Add data to reader.
     *
     * @param array $data
     */
    protected function _addData($data)
    {
        $this->_data = array_merge_recursive($this->_data, $data);
    }

    /**
     * Load config data from cache.
     *
     * @return bool Return true on successful load; false otherwise
     */
    protected function _loadDataFromCache()
    {
        $isLoaded = false;
        if ($this->_cacheState->isEnabled(Magento_Webapi_Model_ConfigAbstract::WEBSERVICE_CACHE_NAME)) {
            $cachedData = $this->_cache->load($this->getCacheId());
            if ($cachedData !== false) {
                $this->_data = unserialize($cachedData);
                $isLoaded = true;
            }
        }
        return $isLoaded;
    }

    /**
     * Save data to cache if it is enabled.
     */
    protected function _saveDataToCache()
    {
        if ($this->_cacheState->isEnabled(Magento_Webapi_Model_ConfigAbstract::WEBSERVICE_CACHE_NAME)) {
            $this->_cache->save(
                serialize($this->_data),
                $this->getCacheId(),
                array(Magento_Webapi_Model_ConfigAbstract::WEBSERVICE_CACHE_TAG)
            );
        }
    }
}
