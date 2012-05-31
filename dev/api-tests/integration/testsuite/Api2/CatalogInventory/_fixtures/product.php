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

/* @var $product Mage_Catalog_Model_Product */
$product = require TESTS_FIXTURES_DIRECTORY . '/_block/Catalog/Product.php';
$product->save();

/* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
$stockItem = require TESTS_FIXTURES_DIRECTORY . '/_block/CatalogInventory/Stock/Item.php';
$stockItem->setProductId($product->getId())
    ->save();

Magento_Test_Webservice::setFixture('product', $product);
Magento_Test_Webservice::setFixture('stockItem', $stockItem->load($stockItem->getId())); // for prepare data
