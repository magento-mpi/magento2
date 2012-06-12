<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_CatalogPermissions
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $permission Enterprise_CatalogPermissions_Model_Permission */
$permission = Mage::getModel('Enterprise_CatalogPermissions_Model_Permission');
$permission->setWebsiteId(Mage::app()->getWebsite()->getId())
    ->setCategoryId(6)
    ->setCustomerGroupId(1)
    ->setGrantCatalogCategoryView(Enterprise_CatalogPermissions_Model_Permission::PERMISSION_DENY)
    ->setGrantCatalogProductPrice(Enterprise_CatalogPermissions_Model_Permission::PERMISSION_DENY)
    ->setGrantCheckoutItems(Enterprise_CatalogPermissions_Model_Permission::PERMISSION_DENY)
    ->save();
