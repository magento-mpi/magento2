<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogPermissions
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $permission Magento_CatalogPermissions_Model_Permission */
$permission = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_CatalogPermissions_Model_Permission');
$permission->setWebsiteId(Mage::app()->getWebsite()->getId())
    ->setCategoryId(6)
    ->setCustomerGroupId(1)
    ->setGrantCatalogCategoryView(Magento_CatalogPermissions_Model_Permission::PERMISSION_DENY)
    ->setGrantCatalogProductPrice(Magento_CatalogPermissions_Model_Permission::PERMISSION_DENY)
    ->setGrantCheckoutItems(Magento_CatalogPermissions_Model_Permission::PERMISSION_DENY)
    ->save();
