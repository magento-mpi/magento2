<?php
/**
 * Webapi ACL cache
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_Webapi_Model_Acl_Cache extends Mage_Core_Model_Acl_Cache
{
    /**
     * @param Mage_Core_Model_Cache_Type_Config $cache
     * @param string $cacheKey
     */
    public function __construct(Mage_Core_Model_Cache_Type_Config $cache, $cacheKey)
    {
        parent::__construct($cache, $cacheKey);
    }
}
