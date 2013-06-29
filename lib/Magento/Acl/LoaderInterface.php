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
interface Magento_Acl_LoaderInterface
{
    /**
     * Populate ACL with data from external storage
     *
     * @abstract
     * @param Magento_Acl $acl
     */
    public function populateAcl(Magento_Acl $acl);
}
