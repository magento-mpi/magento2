<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System / Cache Management / Cache type "Configuration"
 */
class Magento_Core_Model_Cache_Type_Config extends \Magento\Cache\Frontend\Decorator\TagScope
    implements \Magento\Config\CacheInterface
{
    /**
     * Cache type code unique among all cache types
     */
    const TYPE_IDENTIFIER = 'config';

    /**
     * Cache tag used to distinguish the cache type from all other cache
     */
    const CACHE_TAG = 'CONFIG';

    /**
     * @param Magento_Core_Model_Cache_Type_FrontendPool $cacheFrontendPool
     */
    public function __construct(Magento_Core_Model_Cache_Type_FrontendPool $cacheFrontendPool)
    {
        parent::__construct($cacheFrontendPool->get(self::TYPE_IDENTIFIER), self::CACHE_TAG);
    }

    /**
     * Retrieve config data
     *
     * @param string $scope
     * @param string $cacheId
     * @return mixed
     */
    public function get($scope, $cacheId)
    {
        return unserialize($this->load($scope . '_' . $cacheId));
    }

    /**
     * Save config data to cache
     *
     * @param mixed $data
     * @param string $scope
     * @param string $cacheId
     */
    public function put($data, $scope, $cacheId)
    {
        $this->save(serialize($data), $scope . '_' . $cacheId);
    }
}
