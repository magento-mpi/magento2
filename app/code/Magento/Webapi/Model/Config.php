<?php

/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model;

/**
 * Web API Config Model.
 *
 * This is a parent class for storing information about Web API. Most of it is needed by REST.
 */
class Config
{
    const CACHE_ID = 'webapi';

    /**
     * Pattern for Web API interface name.
     */
    const SERVICE_CLASS_PATTERN = '/^(.+?)\\\\(.+?)\\\\Service(\\\\.+)+(V\d+)Interface$/';

    /**
     * @var \Magento\App\Cache\Type\Config
     */
    protected $_configCacheType;

    /**
     * @var \Magento\Webapi\Model\Config\Reader
     */
    protected $_configReader;

    /**
     * Module configuration reader
     *
     * @var \Magento\Module\Dir\Reader
     */
    protected $_moduleReader;

    /**
     * @var array
     */
    protected $_services;

    /**
     * @param Cache\Type $configCacheType
     * @param Config\Reader $configReader
     */
    public function __construct(
        \Magento\Webapi\Model\Cache\Type $configCacheType,
        \Magento\Webapi\Model\Config\Reader $configReader
    ) {
        $this->_configCacheType = $configCacheType;
        $this->_configReader = $configReader;
    }

    /**
     * Return services loaded from cache if enabled or from files merged previously
     *
     * @return array
     */
    public function getServices()
    {
        if (null === $this->_services) {
            $services = $this->_loadFromCache();
            if ($services && is_string($services)) {
                $this->_services = unserialize($services);
            } else {
                $this->_services = $this->_configReader->read();
                $this->_saveToCache(serialize($this->_services));
            }
        }
        return $this->_services;
    }

    /**
     * Load services from cache
     */
    protected function _loadFromCache()
    {
        return $this->_configCacheType->load(self::CACHE_ID);
    }

    /**
     * Save services into the cache
     *
     * @param string $data serialized version of the webapi registry
     * @return \Magento\Webapi\Model\Config
     */
    protected function _saveToCache($data)
    {
        $this->_configCacheType->save($data, self::CACHE_ID, array(\Magento\Webapi\Model\Cache\Type::CACHE_TAG));
        return $this;
    }
}
