<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
$productData = require dirname(__FILE__) . '/Backend/SimpleProductAllFieldsData.php';
$product = new Mage_Catalog_Model_Product();
$product->setStoreId(0);
$productData['stock_data']['use_config_manage_stock'] = 0;
$websiteIds = array(Mage::app()->getDefaultStoreView()->getWebsiteId());
/** @var $testStore Mage_Core_Model_Store */
$testStore = Magento_Test_Webservice::getFixture('store_on_new_website');
if ($testStore) {
    $websiteIds[] = $testStore->getWebsiteId();
}
$product->setWebsiteIds($websiteIds);

$product->addData($productData)->save();

// to make stock item visible from created product it should be reloaded
$product = Mage::getModel('catalog/product')->load($product->getId());
Magento_Test_Webservice::setFixture('product_simple_all_fields', $product);
