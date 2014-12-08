<?php
/**
 * ACL object cache
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Framework\Acl;

interface CacheInterface
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
     * @return \Magento\Framework\Acl
     */
    public function get();

    /**
     * Save ACL object to cache
     *
     * @param \Magento\Framework\Acl $acl
     * @return void
     */
    public function save(\Magento\Framework\Acl $acl);

    /**
     * Clear ACL instance cache
     *
     * @return void
     */
    public function clean();
}
