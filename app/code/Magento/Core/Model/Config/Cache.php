<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Config;

class Cache
{
    /**
     * Config cache id
     *
     * @var string
     */
    protected $_cacheId = 'config_global';

    /**
     * Container factory model
     *
     * @var \Magento\Core\Model\Config\BaseFactory
     */
    protected $_containerFactory;

    /**
     * @var \Magento\Core\Model\Cache\Type\Config
     */
    protected $_configCacheType;

    /**
     * Cache lifetime in seconds
     *
     * @var int
     */
    protected $_cacheLifetime;

    /**
     * Config container
     *
     * @var \Magento\Core\Model\Config\Base
     */
    protected $_loadedConfig = null;

    /**
     * @param \Magento\Core\Model\Cache\Type\Config $configCacheType
     * @param \Magento\Core\Model\Config\BaseFactory $containerFactory
     */
    public function __construct(
        \Magento\Core\Model\Cache\Type\Config $configCacheType,
        \Magento\Core\Model\Config\BaseFactory $containerFactory
    ) {
        $this->_containerFactory = $containerFactory;
        $this->_configCacheType = $configCacheType;
    }

    /**
     * Set cache lifetime
     *
     * @param int $lifetime
     */
    public function setCacheLifetime($lifetime)
    {
        $this->_cacheLifetime = $lifetime;
    }

    /**
     * Retrieve cache lifetime
     *
     * @return int
     */
    public function getCacheLifeTime()
    {
        return $this->_cacheLifetime;
    }

    /**
     * @return \Magento\Core\Model\ConfigInterface|bool
     */
    public function load()
    {
        if (!$this->_loadedConfig) {
            $config = $this->_configCacheType->load($this->_cacheId);
            if ($config) {
                $this->_loadedConfig = $this->_containerFactory->create($config);
            }
        }
        return $this->_loadedConfig ? : false;
    }

    /**
     * Save config cache
     *
     * @param \Magento\Core\Model\Config\Base $config
     */
    public function save(\Magento\Core\Model\Config\Base $config)
    {
        $this->_configCacheType->save(
            $config->getNode()->asNiceXml('', false), $this->_cacheId, array(), $this->_cacheLifetime
        );
    }

    /**
     * Clean cached data
     *
     * @return bool
     */
    public function clean()
    {
        $this->_loadedConfig = null;
        return $this->_configCacheType->clean();
    }
}
