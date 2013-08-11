<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
$product = Mage::getModel('Magento_Catalog_Model_Product');
$product->setTypeId('simple')
    ->setAttributeSetId(4)
    ->setStoreId(0)
    ->setName('Simple Product')
    ->setSku('simple-product-' . uniqid())
    ->setPrice(10)
    ->setTaxClassId(0)
    ->setMetaTitle('meta title')
    ->setMetaKeyword('meta keyword')
    ->setMetaDescription('meta description')
    ->setVisibility(Magento_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
    ->setStatus(Magento_Catalog_Model_Product_Status::STATUS_ENABLED)
    ->setStockData(
    array(
        'use_config_manage_stock' => 1,
        'qty' => 100,
        'is_qty_decimal' => 0,
        'is_in_stock' => 1,
    )
)
    ->save();
// to make stock item visible from created product it should be reloaded
$product = Mage::getModel('Magento_Catalog_Model_Product')->load($product->getId());
Mage::register('product_simple', $product);
