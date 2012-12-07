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

$stockItemData = require TEST_FIXTURE_DIR . '/_data/CatalogInventory/Stock/Item/stock_item_data.php';
$stockItem = new Mage_CatalogInventory_Model_Stock_Item();
$stockItem->setData($stockItemData);
return $stockItem;
