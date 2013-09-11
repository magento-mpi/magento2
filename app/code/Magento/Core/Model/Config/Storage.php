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

class Storage extends \Magento\Core\Model\Config\StorageAbstract
{
    /**
     * Resource configuration
     *
     * @var \Magento\Core\Model\Config\Resource
     */
    protected $_resourcesConfig;

    /**
     * @param \Magento\Core\Model\Config\Cache $cache
     * @param \Magento\Core\Model\Config\Loader $loader
     * @param \Magento\Core\Model\Config\BaseFactory $factory
     * @param \Magento\Core\Model\Config\Resource $resourcesConfig
     */
    public function __construct(
        \Magento\Core\Model\Config\Cache $cache,
        \Magento\Core\Model\Config\Loader $loader,
        \Magento\Core\Model\Config\BaseFactory $factory,
        \Magento\Core\Model\Config\Resource $resourcesConfig
    ) {
        parent::__construct($cache, $loader, $factory);
        $this->_resourcesConfig = $resourcesConfig;
    }

    /**
     * Retrieve application configuration
     *
     * @return \Magento\Core\Model\ConfigInterface
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
