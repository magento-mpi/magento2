<?php
/**
 * Application config storage
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Core_Model_Config_Storage_Worker extends Saas_Core_Model_Config_StorageAbstract
{

    /**
     * Config storage modules
     *
     * @var Mage_Core_Model_Config_StorageInterface
     */
    protected $_storageModules;

    /**
     * Config storage locales
     *
     * @var Mage_Core_Model_Config_StorageInterface
     */
    protected $_storageLocales;

    /**
     * Resource configuration
     *
     * @var Mage_Core_Model_Config_Resource
     */
    protected $_resourcesConfig;

    /**
     * @param Mage_Core_Model_Config_Cache $cache
     * @param Mage_Core_Model_Config_LoaderInterface $loader
     * @param Mage_Core_Model_Config_BaseFactory $factory
     * @param Mage_Core_Model_Config_Resource $resourcesConfig
     * @param Mage_Core_Model_Config_StorageInterface $storageModules
     * @param Mage_Core_Model_Config_StorageInterface $storageLocales
     */
    public function __construct(
        Mage_Core_Model_Config_Cache $cache,
        Mage_Core_Model_Config_LoaderInterface $loader,
        Mage_Core_Model_Config_BaseFactory $factory,
        Mage_Core_Model_Config_Resource $resourcesConfig,
        Mage_Core_Model_Config_StorageInterface $storageModules,
        Mage_Core_Model_Config_StorageInterface $storageLocales
    ) {
        parent::__construct($cache, $loader, $factory);
        $this->_resourcesConfig = $resourcesConfig;
        $this->_storageModules = $storageModules;
        $this->_storageLocales = $storageLocales;
    }


    /**
     * Retrieve application configuration
     *
     * @return Mage_Core_Model_ConfigInterface
     */
    public function getConfiguration()
    {
        $config = $this->_cache->load();
        if (false === $config || $this->_cacheInvalidated) {
            $config = $this->_configFactory->create('<config/>');
            $this->_loader->load($config);
            $this->_cache->save($config);
        }
        /*
         * Update resource configuration when total configuration is loaded.
         * Required until resource model is refactored.
         */
        $this->_resourcesConfig->setConfig($config);
        return $config;
    }

    /**
     * Remove configuration cache
     */
    public function removeCache()
    {
        $this->_storageModules->removeCache();
        $this->_storageLocales->removeCache();
        $this->_cacheInvalidated = true;
    }
}
