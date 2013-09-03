<?php
/**
 * Webapi ACL cache
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Webapi_Model_Acl_Cache extends Magento_Core_Model_Acl_Cache
{
    /**
     * @param Magento_Core_Model_Cache_Type_Config $cache
     * @param string $cacheKey
     */
    public function __construct(Magento_Core_Model_Cache_Type_Config $cache, $cacheKey)
    {
        parent::__construct($cache, $cacheKey);
    }
}
