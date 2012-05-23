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

require dirname(dirname(dirname(__FILE__))).'/Categories/_fixtures/new_category_on_new_store.php';

$fixturesDir = realpath(dirname(__FILE__) . '/../../../../../../fixtures');

/* @var $productFixture Mage_Catalog_Model_Product */
$product = require $fixturesDir . '/Catalog/Product.php';
$product->setStoreId(0)
    ->setWebsiteIds(array(Mage::app()->getDefaultStoreView()->getWebsiteId()))
    ->save();
// product should be assigned to website (with appropriate store view) to use store view in rest
$websites = $product->getWebsiteIds();
$websites[] = Magento_Test_Webservice::getFixture('website')->getId();

// to make stock item visible from created product it should be reloaded
$product = Mage::getModel('catalog/product')->load($product->getId());
$product->setStoreId(Magento_Test_Webservice::getFixture('store')->getId())
    ->setWebsiteIds($websites)
    ->save();
Magento_Test_Webservice::setFixture('product_simple', $product);
