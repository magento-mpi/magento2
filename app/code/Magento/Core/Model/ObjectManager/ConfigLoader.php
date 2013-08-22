<?php
/**
 * ObjectManager configuration loader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_ObjectManager_ConfigLoader
{
    /**
     * Config reader factory
     *
     * @var Magento_ObjectManager_Config_Reader_Dom
     */
    protected $_reader;

    /**
     * Cache
     *
     * @var Magento_Cache_FrontendInterface
     */
    protected $_cache;

    /**
     * @param Magento_Config_CacheInterface $cache
     * @param Magento_ObjectManager_Config_Reader_Dom $reader
     */
    public function __construct(
        Magento_Config_CacheInterface $cache,
        Magento_ObjectManager_Config_Reader_Dom $reader
    ) {
        $this->_cache = $cache;
        $this->_reader = $reader;
    }

    /**
     * Load modules DI configuration
     *
     * @param string $area
     * @return array
     */
    public function load($area)
    {
        $cacheId = 'DiConfig';
        $data = $this->_cache->get($area, $cacheId);

        if (!$data) {
            $data = $this->_reader->read($area);
            $this->_cache->put($data, $area, $cacheId);
        }

        return $data;
    }
}
