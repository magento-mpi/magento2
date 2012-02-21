<?php

$fixturesDir = realpath(dirname(__FILE__) . '/../../../../fixtures');

/** @var $product Mage_Catalog_Model_Product */
$product = require $fixturesDir . '/Catalog/Product.php';

/** @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
$stockItem = require $fixturesDir . '/CatalogInventory/Stock/Item.php';

$product->save();
$stockItem->setProductId($product->getId());
//$stockItem->setProduct($product);
$stockItem->save();

Magento_Test_Webservice::setFixture('product', $product);
Magento_Test_Webservice::setFixture('stockItem', $stockItem);
