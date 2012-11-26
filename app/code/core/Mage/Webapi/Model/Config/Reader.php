<?php
/**
 * Config data reader.
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Config_Reader
{
    /**
     * Cache ID for resource config.
     */
    const CONFIG_CACHE_ID = 'API-RESOURCE-CACHE';
    const RESOURCE_CLASS_PATTERN = '/^(.*)_(.*)_Controller_Webapi(_.*)+$/';

    /**
     * @var Zend\Code\Scanner\DirectoryScanner
     */
    protected $_directoryScanner;

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_applicationConfig;

    /**
     * @var Mage_Core_Model_Cache
     */
    protected $_cache;

    /**
     * @var Mage_Webapi_Helper_Data
     */
    protected $_helper;

    /**
     * @var Mage_Webapi_Model_Config_Reader_ClassReflector
     */
    protected $_classReflector;

    /**
     * @var array
     */
    protected $_data;

    public function __construct(
        Mage_Core_Model_Cache $cache,
        Mage_Core_Model_Config $appConfig,
        Mage_Webapi_Helper_Data $helper,
        Mage_Webapi_Model_Config_Reader_ClassReflector $classReflector
    ) {
        $this->_applicationConfig = $appConfig;
        $this->_cache = $cache;
        $this->_helper = $helper;
        $this->_classReflector = $classReflector;
    }

    /**
     * Read configuration data from the action controllers files.
     *
     * @throws InvalidArgumentException
     * @throws LogicException
     * @return array
     */
    public function getData()
    {
        if (!$this->_data) {
            if ($this->_loadDataFromCache()) {
                return $this;
            }

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
                    $data = $this->_classReflector->reflectClassMethods($className);
                    $data['controller'] = $className;
                    $this->_data['resources'][$this->_helper->translateResourceName($className)] = $data;
                }
            }
            $this->_data['types'] = $this->_classReflector->getTypeProcessor()->getTypesData();
            $this->_data['type_to_class_map'] = $this->_classReflector->getTypeProcessor()->getTypeToClassMap();
            $this->_data['rest_routes'] = $this->_classReflector->getRestRoutes();

            if (!isset($this->_data['resources'])) {
                throw new LogicException('Cannot populate config - no action controllers were found.');
            }
            $this->_saveDataToCache();
        }

        return $this->_data;
    }



    /**
     * Load config data from cache.
     *
     * @return bool Return true on successful load; false otherwise
     */
    protected function _loadDataFromCache()
    {
        $isLoaded = false;
        // TODO: move these constants to server?
        if ($this->_cache->canUse(Mage_Webapi_Controller_Dispatcher_Soap::WEBSERVICE_CACHE_NAME)) {
            $cachedData = $this->_cache->load(self::CONFIG_CACHE_ID);
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
        if ($this->_cache->canUse(Mage_Webapi_Controller_Dispatcher_Soap::WEBSERVICE_CACHE_NAME)) {
            $this->_cache->save(
                serialize($this->_data),
                self::CONFIG_CACHE_ID,
                array(Mage_Webapi_Controller_Dispatcher_Soap::WEBSERVICE_CACHE_TAG)
            );
        }
    }

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
                    $directory = $moduleDir . DS . 'Controller' . DS . 'Webapi';
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
}
