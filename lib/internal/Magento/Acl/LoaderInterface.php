<?php
/**
 * Access Control List loader. All classes implementing this interface should have ability to populate ACL object
 * with data (roles/rules/resources) persisted in external storage.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Acl;

interface LoaderInterface
{
    /**
     * Populate ACL with data from external storage
     *
     * @abstract
     * @param \Magento\Acl $acl
     */
    public function populateAcl(\Magento\Acl $acl);
}
