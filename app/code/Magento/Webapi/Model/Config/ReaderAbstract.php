<?php
/**
 * Abstract config data reader.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Config;

abstract class ReaderAbstract
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
     * @var \Zend\Code\Scanner\DirectoryScanner
     */
    protected $_directoryScanner;

    /**
     * @var \Magento\Webapi\Model\Config\Reader\ClassReflectorAbstract
     */
    protected $_classReflector;

    /**
     * @var \Magento\Core\Model\Config
     */
    protected $_applicationConfig;

    /**
     * @var \Magento\Core\Model\CacheInterface
     */
    protected $_cache;

    /**
     * @var \Magento\Core\Model\ModuleListInterface
     */
    protected $_moduleList;

    /**
     * @var array
     */
    protected $_data = array();

    /**
     * @var \Magento\Core\Model\Cache\StateInterface
     */
    protected $_cacheState;

    /**
     * @param \Magento\Webapi\Model\Config\Reader\ClassReflectorAbstract $classReflector
     * @param \Magento\Core\Model\Config $appConfig
     * @param \Magento\Core\Model\CacheInterface $cache
     * @param \Magento\Core\Model\ModuleListInterface $moduleList
     * @param \Magento\Core\Model\Cache\StateInterface $cacheState
     */
    public function __construct(
        \Magento\Webapi\Model\Config\Reader\ClassReflectorAbstract $classReflector,
        \Magento\Core\Model\Config $appConfig,
        \Magento\Core\Model\CacheInterface $cache,
        \Magento\Core\Model\ModuleListInterface $moduleList,
        \Magento\Core\Model\Cache\StateInterface $cacheState
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
     * @return \Zend\Code\Scanner\DirectoryScanner
     */
    public function getDirectoryScanner()
    {
        if (!$this->_directoryScanner) {
            $this->_directoryScanner = new \Zend\Code\Scanner\DirectoryScanner();
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
     * @param \Zend\Code\Scanner\DirectoryScanner $directoryScanner
     */
    public function setDirectoryScanner(\Zend\Code\Scanner\DirectoryScanner $directoryScanner)
    {
        $this->_directoryScanner = $directoryScanner;
    }

    /**
     * Read configuration data from the action controllers files using class reflector.
     *
     * @throws \InvalidArgumentException
     * @throws \LogicException
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
                    throw new \LogicException(sprintf(
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
                throw new \LogicException('Cannot populate config - no action controllers were found.');
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
        if ($this->_cacheState->isEnabled(\Magento\Webapi\Model\ConfigAbstract::WEBSERVICE_CACHE_NAME)) {
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
        if ($this->_cacheState->isEnabled(\Magento\Webapi\Model\ConfigAbstract::WEBSERVICE_CACHE_NAME)) {
            $this->_cache->save(
                serialize($this->_data),
                $this->getCacheId(),
                array(\Magento\Webapi\Model\ConfigAbstract::WEBSERVICE_CACHE_TAG)
            );
        }
    }
}
