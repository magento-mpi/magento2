<?php
/**
 * Application config storage
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config;

abstract class StorageAbstract implements \Magento\Core\Model\Config\StorageInterface
{
    /**
     * Cache storage object
     *
     * @var \Magento\Core\Model\Config\Cache
     */
    protected $_cache;

    /**
     * Configuration loader
     *
     * @var \Magento\Core\Model\Config\LoaderInterface
     */
    protected $_loader;

    /**
     * Configuration loader
     *
     * @var \Magento\Core\Model\Config\BaseFactory
     */
    protected $_configFactory;

    /**
     * @param \Magento\Core\Model\Config\Cache $cache
     * @param \Magento\Core\Model\Config\LoaderInterface $loader
     * @param \Magento\Core\Model\Config\BaseFactory $factory
     */
    public function __construct(
        \Magento\Core\Model\Config\Cache $cache,
        \Magento\Core\Model\Config\LoaderInterface $loader,
        \Magento\Core\Model\Config\BaseFactory $factory
    ) {
        $this->_cache = $cache;
        $this->_loader = $loader;
        $this->_configFactory = $factory;
    }

    /**
     * Get loaded configuration
     *
     * @return \Magento\Core\Model\ConfigInterface
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
