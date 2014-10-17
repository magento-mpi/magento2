<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Model;

use Magento\Webapi\Model\Cache\Type;
use Zend\Code\Reflection\ClassReflection;

/**
 * Web API Cache Processor.
 */
class Processor
{
    const CACHE_ID = 'webapi';

    /**
     * @var \Magento\Framework\App\Cache\Type\Config
     */
    protected $configCacheType;

    /**
     * @param Type $configCacheType
     */
    public function __construct(Type $configCacheType)
    {
        $this->configCacheType = $configCacheType;
    }

    /**
     * Load from cache
     *
     * @param string $cacheId cache to look up from
     * @return string|bool
     */
    public function loadFromCache($cacheId)
    {
        return $this->configCacheType->load($cacheId);
    }

    /**
     * Save into the cache
     *
     * @param string $data serialized version of the webapi registry
     * @param string $cacheId save cache with this id
     * @return $this
     */
    public function saveToCache($data, $cacheId)
    {
        $this->configCacheType->save($data, $cacheId, array(\Magento\Webapi\Model\Cache\Type::CACHE_TAG));
        return $this;
    }
}
