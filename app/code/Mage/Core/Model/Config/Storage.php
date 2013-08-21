<?php
/**
 * Application config storage
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_Storage extends Mage_Core_Model_Config_StorageAbstract
{
    /**
     * Resource configuration
     *
     * @var Mage_Core_Model_Config_Resource
     */
    protected $_resourcesConfig;

    /**
     * @param Mage_Core_Model_Config_Cache $cache
     * @param Mage_Core_Model_Config_Loader $loader
     * @param Mage_Core_Model_Config_BaseFactory $factory
     * @param Mage_Core_Model_Config_Resource $resourcesConfig
     */
    public function __construct(
        Mage_Core_Model_Config_Cache $cache,
        Mage_Core_Model_Config_Loader $loader,
        Mage_Core_Model_Config_BaseFactory $factory,
        Mage_Core_Model_Config_Resource $resourcesConfig
    ) {
        parent::__construct($cache, $loader, $factory);
        $this->_resourcesConfig = $resourcesConfig;
    }

    /**
     * Retrieve application configuration
     *
     * @return Mage_Core_Model_ConfigInterface
     */
    public function getConfiguration()
    {
        $config = $this->_cache->load();
        if (false === $config) {
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
        $this->_cache->clean();
    }
}
