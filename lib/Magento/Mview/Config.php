<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Mview;

class Config extends \Magento\Config\Data implements ConfigInterface
{
    /**
     * @param \Magento\Mview\Config\Reader $reader
     * @param \Magento\Config\CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        \Magento\Mview\Config\Reader $reader,
        \Magento\Config\CacheInterface $cache,
        $cacheId = 'mview_config'
    ) {
        parent::__construct($reader, $cache, $cacheId);
    }

    /**
     * Get indexer's config
     *
     * @return mixed
     */
    public function getAll()
    {
        return $this->getAll();
    }

    /**
     * Get config value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->get($key, $default);
    }
}
