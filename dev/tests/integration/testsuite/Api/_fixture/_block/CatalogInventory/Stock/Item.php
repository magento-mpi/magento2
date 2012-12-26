<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$stockItemData = require '_fixture/_data/CatalogInventory/Stock/Item/stock_item_data.php';
$stockItem = Mage::getModel('Mage_CatalogInventory_Model_Stock_Item');
$stockItem->setData($stockItemData);
return $stockItem;
