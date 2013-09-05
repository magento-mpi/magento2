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
     * @var \Magento\ObjectManager\Config\Reader\Dom
     */
    protected $_reader;

    /**
     * Cache
     *
     * @var \Magento\Cache\FrontendInterface
     */
    protected $_cache;

    /**
     * @param \Magento\Config\CacheInterface $cache
     * @param \Magento\ObjectManager\Config\Reader\Dom $reader
     */
    public function __construct(
        \Magento\Config\CacheInterface $cache,
        \Magento\ObjectManager\Config\Reader\Dom $reader
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
