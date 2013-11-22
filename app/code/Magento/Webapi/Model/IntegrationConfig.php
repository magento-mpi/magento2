<?php

/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model;

/**
 * Integration Api Config Model.
 *
 * This is a parent class for storing information about Integrations.
 */
class IntegrationConfig
{
    const CACHE_ID = 'integration-api';

    /**
     * @var \Magento\App\Cache\Type\Config
     */
    protected $_configCacheType;

    /**
     * @var \Magento\Webapi\Model\Config\Integration\Reader
     */
    protected $_configReader;

    /**
     * @var array
     */
    protected $_integrations;


    /**
     * @param Cache\TypeIntegration $configCacheType
     * @param Config\Integration\Reader $configReader
     */
    public function __construct(
        Cache\TypeIntegration $configCacheType,
        Config\Integration\Reader $configReader
    ) {
        $this->_configCacheType = $configCacheType;
        $this->_configReader = $configReader;
    }

    /**
     * Return integrations loaded from cache if enabled or from files merged previously
     *
     * @return array
     */
    public function getIntegrations()
    {
        if (null === $this->_integrations) {
            $integrations = $this->_loadFromCache();
            if ($integrations && is_string($integrations)) {
                $this->_integrations = unserialize($integrations);
            } else {
                $this->_integrations = $this->_configReader->read();
                $this->_saveToCache(serialize($this->_integrations));
            }
        }
        return $this->_integrations;
    }

    /**
     * Load integrations from cache
     */
    protected function _loadFromCache()
    {
        return $this->_configCacheType->load(self::CACHE_ID);
    }

    /**
     * Save integrations into the cache
     *
     * @param string $data serialized version of the Integration registry
     * @return \Magento\Webapi\Model\IntegrationConfig
     */
    protected function _saveToCache($data)
    {
        $this->_configCacheType->save(
            $data,
            self::CACHE_ID,
            array(\Magento\Webapi\Model\Cache\TypeIntegration::CACHE_TAG)
        );
        return $this;
    }
}
