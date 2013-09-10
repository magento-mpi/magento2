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
 * System / Cache Management / Cache type "Legacy API Configuration"
 *
 * @todo utilize the class for all manipulations with the cache type
 */
class Magento_Api_Model_Cache_Type extends Magento_Cache_Frontend_Decorator_TagScope
{
    /**
     * Cache type code unique among all cache types
     */
    const TYPE_IDENTIFIER = 'config_api';

    /**
     * Cache tag used to distinguish the cache type from all other cache
     */
    const CACHE_TAG = 'CONFIG_API';

    /**
     * @param Magento_Core_Model_Cache_Type_FrontendPool $frontend
     */
    public function __construct(Magento_Core_Model_Cache_Type_FrontendPool $frontend)
    {
        parent::__construct($frontend->get(self::TYPE_IDENTIFIER), self::CACHE_TAG);
    }
}
