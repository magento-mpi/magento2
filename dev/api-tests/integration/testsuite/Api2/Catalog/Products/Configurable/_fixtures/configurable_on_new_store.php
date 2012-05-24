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
require "configurable_with_assigned_products.php";
require dirname(__FILE__) . "/../../../_fixtures/store_on_new_website.php";

/** @var $configurableProduct Mage_Catalog_Model_Product */
$configurableProduct = Magento_Test_Webservice::getFixture('product_configurable');

$websiteIds = array(Mage::app()->getDefaultStoreView()->getWebsiteId());
/** @var $testStore Mage_Core_Model_Store */
$testStore = Magento_Test_Webservice::getFixture('store_on_new_website');
if ($testStore) {
    $websiteIds[] = $testStore->getWebsiteId();
}
$configurableProduct->setWebsiteIds($websiteIds);
$configurableProduct->save();

Magento_Test_Webservice::setFixture('product_configurable', $configurableProduct);
