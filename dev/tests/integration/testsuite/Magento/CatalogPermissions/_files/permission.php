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

/** @var $permission \Magento\CatalogPermissions\Model\Permission */
$permission = Mage::getModel('Magento\CatalogPermissions\Model\Permission');
$permission->setWebsiteId(Mage::app()->getWebsite()->getId())
    ->setCategoryId(6)
    ->setCustomerGroupId(1)
    ->setGrantCatalogCategoryView(\Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY)
    ->setGrantCatalogProductPrice(\Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY)
    ->setGrantCheckoutItems(\Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY)
    ->save();
