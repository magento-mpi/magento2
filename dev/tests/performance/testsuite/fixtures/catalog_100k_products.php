<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$pattern = array(
    '_attribute_set' => 'Default',
    '_type' => Magento_Catalog_Model_Product_Type::TYPE_SIMPLE,
    '_product_websites' => 'base',
    'name' => 'Product %s',
    'short_description' => 'Short desc %s',
    'weight' => 1,
    'description' => 'Description %s',
    'sku' => 'product_dynamic_%s',
    'price' => 10,
    'visibility' => Magento_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
    'status' => Magento_Catalog_Model_Product_Status::STATUS_ENABLED,
    'tax_class_id' => 0,

    // actually it saves without stock data, but by default system won't show on the frontend products out of stock
    'is_in_stock' => 1,
    'qty' => 100500,
    'use_config_min_qty' => '1',
    'use_config_backorders' => '1',
    'use_config_min_sale_qty' => '1',
    'use_config_max_sale_qty' => '1',
    'use_config_notify_stock_qty' => '1',
    'use_config_manage_stock' => '1',
    'use_config_qty_increments' => '1',
    'use_config_enable_qty_inc' => '1',
    'stock_id' => Magento_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID,
);
$generator = new Magento_TestFramework_ImportExport_Fixture_Generator($pattern, 100000);
/** @var Magento_ImportExport_Model_Import $import */
$import = Mage::getModel(
    'Magento_ImportExport_Model_Import',
    array('entity' => 'catalog_product', 'behavior' => 'append')
);
// it is not obvious, but the validateSource() will actually save import queue data to DB
$import->validateSource($generator);
// this converts import queue into actual entities
$import->importSource();
