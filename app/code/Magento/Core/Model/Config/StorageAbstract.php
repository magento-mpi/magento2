<?php
/**
 * Application config storage
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class Magento_Core_Model_Config_StorageAbstract implements Magento_Core_Model_Config_StorageInterface
{
    /**
     * Cache storage object
     *
     * @var Magento_Core_Model_Config_Cache
     */
    protected $_cache;

    /**
     * Configuration loader
     *
     * @var Magento_Core_Model_Config_LoaderInterface
     */
    protected $_loader;

    /**
     * Configuration loader
     *
     * @var Magento_Core_Model_Config_BaseFactory
     */
    protected $_configFactory;

    /**
     * @param Magento_Core_Model_Config_Cache $cache
     * @param Magento_Core_Model_Config_LoaderInterface $loader
     * @param Magento_Core_Model_Config_BaseFactory $factory
     */
    public function __construct(
        Magento_Core_Model_Config_Cache $cache,
        Magento_Core_Model_Config_LoaderInterface $loader,
        Magento_Core_Model_Config_BaseFactory $factory
    ) {
        $this->_cache = $cache;
        $this->_loader = $loader;
        $this->_configFactory = $factory;
    }

    /**
     * Get loaded configuration
     *
     * @return Magento_Core_Model_ConfigInterface
     */
    public function getConfiguration()
    {
        $config = $this->_cache->load();
        if (false === $config) {
            $config = $this->_configFactory->create('<config/>');
            $this->_loader->load($config);
        }
        return $config;
    }

    /**
     * Remove configuration cache
     */
    public function removeCache()
    {

    }
}
