<?php
/**
 * Abstract config data reader.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class Mage_Core_Service_Config_ReaderAbstract
{
    /**
     * Cache ID for resource config.
     */
    const CONFIG_CACHE_ID = 'API-RESOURCE-CACHE';

    /**
     * Pattern for API action controllers class name.
     */
    const RESOURCE_CLASS_PATTERN = '/^(.*)_(.*)_Service(_.*)+$/';

    /**
     * @var Zend\Code\Scanner\DirectoryScanner
     */
    protected $_directoryScanner;

    /**
     * @var Mage_Core_Service_Config_Reader_ClassReflectorAbstract
     */
    protected $_classReflector;

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_applicationConfig;

    /**
     * @var Mage_Core_Model_CacheInterface
     */
    protected $_cache;

    /**
     * @var Mage_Webapi_Helper_Config
     */
    protected $_configHelper;

    /**
     * @var array
     */
    protected $_data = array();

    /**
     * Construct config reader.
     *
     * @param Mage_Core_Service_Config_Reader_ClassReflectorAbstract $classReflector
     * @param Mage_Core_Model_Config $appConfig
     * @param Mage_Core_Model_CacheInterface $cache
     * @param Mage_Webapi_Helper_Config $configHelper
     */
    public function __construct(
        Mage_Core_Service_Config_Reader_ClassReflectorAbstract $classReflector,
        Mage_Core_Model_Config $appConfig,
        Mage_Core_Model_CacheInterface $cache,
        Mage_Webapi_Helper_Config $configHelper
    ) {
        $this->_classReflector = $classReflector;
        $this->_applicationConfig = $appConfig;
        $this->_cache = $cache;
        $this->_configHelper = $configHelper;
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
            /** @var Mage_Core_Model_Config_Element $module */
            foreach ($this->_applicationConfig->getNode('modules')->children() as $moduleName => $module) {
                if ($module->is('active')) {
                    /** Invalid type is specified to retrieve path to module directory. */
                    $moduleDir = $this->_applicationConfig->getModuleDir('invalid_type', $moduleName);
                    $directory = $moduleDir . DS . 'Service';
                    if (is_dir($directory)) {
                        $this->_directoryScanner->addDirectory($directory);
                    }
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
                } elseif (empty($classes)) {
                    /** No classes defined in current file. */
                    continue;
                }
                /** @var \Zend\Code\Scanner\ClassScanner $class */
                $class = current($classes);
                $className = $class->getName();
                if (preg_match(self::RESOURCE_CLASS_PATTERN, $className)) {
                    $serverReflection = new Zend\Server\Reflection;
                    $serviceAnnotation = $this->_configHelper->getAnnotationValue(
                        $serverReflection->reflectClass($className),
                        'Service'
                    );
                    if (!is_string($serviceAnnotation)) {
                        /**
                         * Services exposed via Web API must have @Service annotation defined on class level.
                         */
                        continue;
                    }
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
        if ($this->_cache->canUse(Mage_Core_Service_ConfigAbstract::WEBSERVICE_CACHE_NAME)) {
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
        if ($this->_cache->canUse(Mage_Core_Service_ConfigAbstract::WEBSERVICE_CACHE_NAME)) {
            $this->_cache->save(
                serialize($this->_data),
                $this->getCacheId(),
                array(Mage_Core_Service_ConfigAbstract::WEBSERVICE_CACHE_TAG)
            );
        }
    }
}
