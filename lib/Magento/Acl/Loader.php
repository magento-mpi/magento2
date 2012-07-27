<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Framework
 * @subpackage  ACL
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Access Control List loader. All classes implementing this interface should have ability to populate ACL object
 * with data (roles/rules/resources) persisted in external storage.
 */
interface Magento_Acl_Loader
{
    /**
     * Populate ACL with data from external storage
     *
     * @abstract
     * @param Magento_Acl $acl
     */
    public function populateAcl(Magento_Acl $acl);
}
