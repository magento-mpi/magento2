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
 * System / Cache Management / Cache type "EAV types and attributes"
 *
 * @todo utilize the class for all manipulations with the cache type
 */
namespace Magento\Eav\Model\Cache;

class Type extends \Magento\Cache\Frontend\Decorator\TagScope
{
    /**
     * Cache type code unique among all cache types
     */
    const TYPE_IDENTIFIER = 'eav';

    /**
     * Cache tag used to distinguish the cache type from all other cache
     */
    const CACHE_TAG = 'EAV';

    /**
     * @param \Magento\Core\Model\Cache\Type\FrontendPool $cacheFrontendPool
     */
    public function __construct(\Magento\Core\Model\Cache\Type\FrontendPool $cacheFrontendPool)
    {
        parent::__construct($cacheFrontendPool->get(self::TYPE_IDENTIFIER), self::CACHE_TAG);
    }
}
