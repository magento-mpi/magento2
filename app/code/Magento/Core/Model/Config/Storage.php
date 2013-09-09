<?php
/**
 * Application config storage
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Config_Storage extends Magento_Core_Model_Config_StorageAbstract
{
    /**
     * Resource configuration
     *
     * @var Magento_Core_Model_Config_Resource
     */
    protected $_resourcesConfig;

    /**
     * @param Magento_Core_Model_Config_Cache $cache
     * @param Magento_Core_Model_Config_Loader $loader
     * @param Magento_Core_Model_Config_BaseFactory $factory
     * @param Magento_Core_Model_Config_Resource $resourcesConfig
     */
    public function __construct(
        Magento_Core_Model_Config_Cache $cache,
        Magento_Core_Model_Config_Loader $loader,
        Magento_Core_Model_Config_BaseFactory $factory,
        Magento_Core_Model_Config_Resource $resourcesConfig
    ) {
        parent::__construct($cache, $loader, $factory);
        $this->_resourcesConfig = $resourcesConfig;
    }

    /**
     * Retrieve application configuration
     *
     * @return Magento_Core_Model_ConfigInterface
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
