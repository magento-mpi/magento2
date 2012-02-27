<?php

$fixturesDir = realpath(dirname(__FILE__) . '/../../../../fixtures');

/* @var $product Mage_Catalog_Model_Product */
$product = require $fixturesDir . '/Catalog/Product.php';
$product->save();

/* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
$stockItem = require $fixturesDir . '/CatalogInventory/Stock/Item.php';
$stockItem->setProductId($product->getId())
    ->save();

Magento_Test_Webservice::setFixture('product', $product);
Magento_Test_Webservice::setFixture('stockItem', $stockItem->load($stockItem->getId())); // for prepare data
