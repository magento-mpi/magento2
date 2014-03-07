<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System / Cache Management / Cache type "Configuration"
 */
namespace Magento\App\Cache\Type;

class Config extends \Magento\Cache\Frontend\Decorator\TagScope
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
     * @param \Magento\App\Cache\Type\FrontendPool $cacheFrontendPool
     */
    public function __construct(\Magento\App\Cache\Type\FrontendPool $cacheFrontendPool)
    {
        parent::__construct($cacheFrontendPool->get(self::TYPE_IDENTIFIER), self::CACHE_TAG);
    }
}
