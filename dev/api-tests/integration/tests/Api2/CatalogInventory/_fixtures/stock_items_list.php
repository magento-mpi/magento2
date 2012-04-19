<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

define('COUNT_STOCK_ITEMS_LIST', 3);

$fixturesDir = realpath(dirname(__FILE__) . '/../../../../fixtures');

/* @var $product Mage_Catalog_Model_Product */
$productFixture = require $fixturesDir . '/Catalog/Product.php';

/* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
$stockItemFixture = require $fixturesDir . '/CatalogInventory/Stock/Item.php';

$products = array();
$stockItems = array();
for ($i = 0; $i < COUNT_STOCK_ITEMS_LIST; $i++) {
    $product = clone $productFixture;
    $products[] = $product->setSku('simple-product-' . microtime())->save();

    $stockItem = clone $stockItemFixture;
    $stockItems[] = $stockItem->setProductId($product->getId())->save();
}

Magento_Test_Webservice::setFixture('cataloginventory_stock_products', $products);
Magento_Test_Webservice::setFixture('cataloginventory_stock_items', $stockItems);
