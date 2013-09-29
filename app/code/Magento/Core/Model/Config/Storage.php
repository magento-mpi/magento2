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
     * @param Magento_Core_Model_Config_Cache $cache
     * @param Magento_Core_Model_Config_Loader $loader
     * @param Magento_Core_Model_Config_BaseFactory $factory
     */
    public function __construct(
        Magento_Core_Model_Config_Cache $cache,
        Magento_Core_Model_Config_Loader $loader,
        Magento_Core_Model_Config_BaseFactory $factory
    ) {
        parent::__construct($cache, $loader, $factory);
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
