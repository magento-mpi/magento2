<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
$productData = require '_fixture/_data/Catalog/Product/Simple/simple_product_all_fields_data.php';
$product = Mage::getModel('Mage_Catalog_Model_Product');
$product->setStoreId(0);
$productData['stock_data']['use_config_manage_stock'] = 0;
$websiteIds = array(Mage::app()->getDefaultStoreView()->getWebsiteId());
/** @var $testStore Mage_Core_Model_Store */
$testStore = Mage::registry('store_on_new_website');
if ($testStore) {
    $websiteIds[] = $testStore->getWebsiteId();
}
$product->setWebsiteIds($websiteIds);

$product->addData($productData)->save();

// to make stock item visible from created product it should be reloaded
$product = Mage::getModel('Mage_Catalog_Model_Product')->load($product->getId());
Mage::register('product_simple_all_fields', $product);
