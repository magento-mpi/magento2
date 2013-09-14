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
 * System / Cache Management / Cache type "Web Services Configuration"
 */
class Magento_Webapi_Model_Cache_Type extends Magento_Cache_Frontend_Decorator_TagScope
{
    /**
     * Cache type code unique among all cache types
     */
    const TYPE_IDENTIFIER = 'config_webservice';

    /**
     * Cache tag used to distinguish the cache type from all other cache
     */
    const CACHE_TAG = 'WEBSERVICE';

    /**
     * @param Magento_Core_Model_Cache_Type_FrontendPool $cacheFrontendPool
     */
    public function __construct(Magento_Core_Model_Cache_Type_FrontendPool $cacheFrontendPool)
    {
        parent::__construct($cacheFrontendPool->get(self::TYPE_IDENTIFIER), self::CACHE_TAG);
    }
}
