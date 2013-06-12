<?php
/**
 * ACL object cache
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
interface Magento_Acl_CacheInterface
{
    /**
     * Check whether ACL object is in cache
     *
     * @return bool
     */
    public function has();

    /**
     * Retrieve ACL object from cache
     *
     * @return Magento_Acl
     */
    public function get();

    /**
     * Save ACL object to cache
     *
     * @param Magento_Acl $acl
     */
    public function save(Magento_Acl $acl);

    /**
     * Clear ACL instance cache
     */
    public function clean();
}
