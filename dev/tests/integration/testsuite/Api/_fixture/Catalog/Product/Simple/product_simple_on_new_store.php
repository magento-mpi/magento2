<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
require '_fixture/Catalog/Category/category_on_new_store.php';

/* @var $product Mage_Catalog_Model_Product */
$product = require '_fixture/_block/Catalog/Product.php';
$product->setStoreId(0)
    ->setWebsiteIds(array(Mage::app()->getDefaultStoreView()->getWebsiteId()))
    ->save();
// product should be assigned to website (with appropriate store view) to use store view in rest
$websites = $product->getWebsiteIds();
$websites[] = Mage::registry('website')->getId();

// to make stock item visible from created product it should be reloaded
$product = Mage::getModel('Mage_Catalog_Model_Product')->load($product->getId());
$product->setStoreId(Mage::registry('store')->getId())
    ->setWebsiteIds($websites)
    ->save();
Mage::register('product_simple', $product);
