<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Model;

use Magento\Webapi\Model\Cache\Type as WebapiCacheType;
use Zend\Code\Reflection\ClassReflection;

/**
 * Web API cache.
 */
class Cache
{
    /**
     * @var WebapiCacheType
     */
    protected $cache;

    /**
     * Initialize dependencies.
     *
     * @param WebapiCacheType $webapiCache
     */
    public function __construct(WebapiCacheType $webapiCache)
    {
        $this->cache = $webapiCache;
    }

    /**
     * Load serialized data from the cache.
     *
     * @param string $cacheId cache to look up from
     * @return string|bool
     */
    public function loadFromCache($cacheId)
    {
        return $this->cache->load($cacheId);
    }

    /**
     * Save serialized data to the cache.
     *
     * @param string $data
     * @param string $cacheId
     * @return $this
     */
    public function saveToCache($data, $cacheId)
    {
        $this->cache->save($data, $cacheId, array(WebapiCacheType::CACHE_TAG));
        return $this;
    }
}
