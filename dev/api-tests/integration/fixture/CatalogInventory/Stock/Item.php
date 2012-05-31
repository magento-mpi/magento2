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

$stockItem = new Mage_CatalogInventory_Model_Stock_Item;
$stockItem->setData(array(
    'stock_id'                => Mage_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID,
    'use_config_manage_stock' => 1,
    'qty'                     => mt_rand(100, 1000),
    'is_qty_decimal'          => 0,
    'is_in_stock'             => 1,
));
return $stockItem;
