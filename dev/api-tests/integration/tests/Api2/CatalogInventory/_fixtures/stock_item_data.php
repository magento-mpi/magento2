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

return array(
    'stock_id'                => Mage_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID,
    'use_config_manage_stock' => 0,
    'qty'                     => mt_rand(1, 125),
    'is_qty_decimal'          => 1,
    'is_in_stock'             => mt_rand(0, 1),
);
