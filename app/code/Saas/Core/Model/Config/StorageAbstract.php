<?php
/**
 * Application config storage
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class Saas_Core_Model_Config_StorageAbstract implements Mage_Core_Model_Config_StorageInterface
{
    /**
     * Cache storage object
     *
     * @var Mage_Core_Model_Config_Cache
     */
    protected $_cache;

    /**
     * Configuration loader
     *
     * @var Mage_Core_Model_Config_LoaderInterface
     */
    protected $_loader;

    /**
     * Configuration loader
     *
     * @var Mage_Core_Model_Config_BaseFactory
     */
    protected $_configFactory;

    /**
     * Cache invalidation flag
     *
     * @var bool
     */
    protected $_cacheInvalidated = false;

    /**
     * @param Mage_Core_Model_Config_Cache $cache
     * @param Mage_Core_Model_Config_LoaderInterface $loader
     * @param Mage_Core_Model_Config_BaseFactory $factory
     */
    public function __construct(
        Mage_Core_Model_Config_Cache $cache,
        Mage_Core_Model_Config_LoaderInterface $loader,
        Mage_Core_Model_Config_BaseFactory $factory
    ) {
        $this->_cache = $cache;
        $this->_loader = $loader;
        $this->_configFactory = $factory;
    }

    /**
     * Get loaded configuration
     *
     * @return Mage_Core_Model_ConfigInterface
     */
    public function getConfiguration()
    {
        $config = $this->_cache->load();
        if (false === $config || $this->_cacheInvalidated) {
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
        $this->_cacheInvalidated = true;
    }
}
