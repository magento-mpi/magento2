<?php
include "store_on_new_website.php";
$fixturesDir = realpath(dirname(__FILE__) . '/../../../../fixtures');

$websiteIds = array(Mage::app()->getDefaultStoreView()->getWebsiteId());
/** @var $testStore Mage_Core_Model_Store */
$testStore = Magento_Test_Webservice::getFixture('store_on_new_website');
if ($testStore) {
    $websiteIds[] = $testStore->getWebsiteId();
}
$products = array();
for ($i = 1; $i <= 3; $i++) {
    /* @var $product Mage_Catalog_Model_Product */
    $product = require $fixturesDir . '/Catalog/Product.php';
    $fieldsToCustomize = array('name', 'description', 'short_description');
    foreach ($fieldsToCustomize as $field) {
        $product->setData($field, "$field-$i");
    }
    $product->save();
    $products[] = $product;
}
$firstProduct = reset($products);
$firstProduct->setWebsiteIds($websiteIds);
$firstProduct->save();

Magento_Test_Webservice::setFixture('products', $products);
