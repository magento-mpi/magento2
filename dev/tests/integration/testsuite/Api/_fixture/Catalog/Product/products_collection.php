<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require '_fixture/Core/Store/store_on_new_website.php';
$websiteIds = array(Mage::app()->getDefaultStoreView()->getWebsiteId());
/** @var $testStore Mage_Core_Model_Store */
$testStore = Mage::registry('store_on_new_website');
if ($testStore) {
    $websiteIds[] = $testStore->getWebsiteId();
}
$products = array();
for ($i = 1; $i <= 3; $i++) {
    /* @var $product Mage_Catalog_Model_Product */
    $product = require '_fixture/_block/Catalog/Product.php';
    $product->setStoreId(0);
    if ($i == 1) {
        $product->setPrice(99.5);
        $product->setWebsiteIds($websiteIds);
    }
    $fieldsToCustomize = array('name', 'description', 'short_description');
    foreach ($fieldsToCustomize as $field) {
        $product->setData($field, "$field-$i");
    }
    $product->save();
    $products[] = $product;
}
PHPUnit_Framework_TestCase::setFixture('products', $products);
