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

class Storage extends \Magento\Core\Model\Config\AbstractStorage
{
    /**
     * @param \Magento\Core\Model\Config\Cache $cache
     * @param \Magento\Core\Model\Config\Loader $loader
     * @param \Magento\Core\Model\Config\BaseFactory $factory
     */
    public function __construct(
        \Magento\Core\Model\Config\Cache $cache,
        \Magento\Core\Model\Config\Loader $loader,
        \Magento\Core\Model\Config\BaseFactory $factory
    ) {
        parent::__construct($cache, $loader, $factory);
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
