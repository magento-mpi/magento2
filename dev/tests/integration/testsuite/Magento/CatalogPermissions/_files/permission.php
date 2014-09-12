<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $permission \Magento\CatalogPermissions\Model\Permission */
$permission = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\CatalogPermissions\Model\Permission'
);
$permission->setWebsiteId(
    \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
        'Magento\Framework\StoreManagerInterface'
    )->getWebsite()->getId()
)->setCategoryId(
    6
)->setCustomerGroupId(
    1
)->setGrantCatalogCategoryView(
    \Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY
)->setGrantCatalogProductPrice(
    \Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY
)->setGrantCheckoutItems(
    \Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY
)->save();

$installer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Catalog\Model\Resource\Setup',
    array('resourceName' => 'catalog_setup')
);

/** @var $product \Magento\Catalog\Model\Product */
$product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Product');
$product->setTypeId(
    \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
)->setId(
    5
)->setAttributeSetId(
    $installer->getAttributeSetId('catalog_product', 'Default')
)->setStoreId(
    1
)->setWebsiteIds(
    array(1)
)->setName(
    'Simple Product Two'
)->setSku(
    '12345'
)->setPrice(
    45.67
)->setWeight(
    56
)->setStockData(
    array('use_config_manage_stock' => 0)
)->setCategoryIds(
    array(6)
)->setVisibility(
    \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH
)->setStatus(
    \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
)->save();
