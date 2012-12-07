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

$taxClasses = Mage::getResourceModel('Mage_Tax_Model_Resource_Class_Collection')->toArray();
$taxClass = reset($taxClasses['items']);

return array(
    'name' => 'Test_new',
    'description' => 'Test description_new',
    'short_description' => 'Test short description_new',
    'news_from_date' => '02/16/2013',
    'news_to_date' => '16.02.2013',
    'status' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED,
    'url_key' => 'test-new',
    'visibility' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
    'is_returnable' => 0,
    'price' => 15.50,
    'special_price' => 15.2,
    'special_from_date' => '02/16/2013',
    'special_to_date' => '03/17/2013',
    'msrp_enabled' => 0,
    'msrp_display_actual_price_type' => Mage_Catalog_Model_Product_Attribute_Source_Msrp_Type::TYPE_IN_CART,
    'msrp' => 15.01,
    'tax_class_id' => $taxClass['class_id'],
    'meta_title' => 'Test title_new',
    'meta_keyword' => 'Test keyword_new',
    'meta_description' => 'Test description_new',
    'custom_design' => 'default/default/default',
    'custom_design_from' => date('Y-m-d', time() + 31*24*3600),
    'custom_design_to' => date('Y-m-d', time() + 12*31*24*3600),
    'custom_layout_update' => '<xml><layout>Test Custom Layout Update_new</layout></xml>',
    'page_layout' => 'empty',
    'gift_wrapping_available' => 0,
    'gift_wrapping_price' => 15.56,
    'stock_data' => array(
        'manage_stock' => 1,
        'qty' => 10,
        'min_qty' => 10.56,
        'min_sale_qty' => 10,
        'max_sale_qty' => 10,
        'is_qty_decimal' => 0,
        'backorders' => 1,
        'notify_stock_qty' => -500.99,
        'enable_qty_increments' => 0,
        'is_in_stock' => 1
    )
);
