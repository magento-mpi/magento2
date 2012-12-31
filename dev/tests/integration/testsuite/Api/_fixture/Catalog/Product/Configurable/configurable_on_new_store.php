<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
require "configurable_with_assigned_products.php";
require '_fixture/Core/Store/store_on_new_website.php';

/** @var $configurableProduct Mage_Catalog_Model_Product */
$configurableProduct = Mage::registry('product_configurable');

$websiteIds = array(Mage::app()->getDefaultStoreView()->getWebsiteId());
/** @var $testStore Mage_Core_Model_Store */
$testStore = Mage::registry('store_on_new_website');
if ($testStore) {
    $websiteIds[] = $testStore->getWebsiteId();
}
$configurableProduct->setWebsiteIds($websiteIds);
$configurableProduct->save();

Mage::register('product_configurable', $configurableProduct);
