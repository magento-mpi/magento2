<?php
/**
 * {license_notice}
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require TEST_FIXTURE_DIR . '/Core/Store/store_on_new_website.php';
$websiteIds = array(Mage::app()->getDefaultStoreView()->getWebsiteId());
/** @var $testStore Mage_Core_Model_Store */
$testStore = Magento_Test_Webservice::getFixture('store_on_new_website');
if ($testStore) {
    $websiteIds[] = $testStore->getWebsiteId();
}
$products = array();
for ($i = 1; $i <= 3; $i++) {
    /* @var $product Mage_Catalog_Model_Product */
    $product = require TEST_FIXTURE_DIR . '/_block/Catalog/Product.php';
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
Magento_Test_Webservice::setFixture('products', $products);
