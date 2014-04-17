<?php
/**
 * ObjectManager configuration loader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\ObjectManager;

class ConfigLoader
{
    /**
     * Config reader factory
     *
     * @var \Magento\Framework\ObjectManager\Config\Reader\Dom
     */
    protected $_reader;

    /**
     * Cache
     *
     * @var \Magento\Cache\FrontendInterface
     */
    protected $_cache;

    /**
     * @param \Magento\Framework\Config\CacheInterface $cache
     * @param \Magento\Framework\ObjectManager\Config\Reader\Dom $reader
     */
    public function __construct(
        \Magento\Framework\Config\CacheInterface $cache,
        \Magento\Framework\ObjectManager\Config\Reader\Dom $reader
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
        $cacheId = $area . '::DiConfig';
        $data = $this->_cache->load($cacheId);

        if (!$data) {
            $data = $this->_reader->read($area);
            $this->_cache->save(serialize($data), $cacheId);
        } else {
            $data = unserialize($data);
        }

        return $data;
    }
}
