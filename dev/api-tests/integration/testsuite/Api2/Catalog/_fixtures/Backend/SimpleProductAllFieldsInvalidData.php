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

/** @var $entityType Mage_Eav_Model_Entity_Type */
$entityType = Mage::getModel('Mage_Eav_Model_Entity_Type')->loadByCode('catalog_product');
$taxClasses = Mage::getResourceModel('Mage_Tax_Model_Resource_Class_Collection')->toArray();
$taxClass = reset($taxClasses['items']);

return array(
    'type_id' => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
    'attribute_set_id' => $entityType->getDefaultAttributeSetId(),
    'name' => '\'Test Name Sql',
    'description' => 'Test \' description',
    'short_description' => 'Test Short description Sql \'',
    'sku' => str_pad('', 65, 'a'),
    'weight' =>  -10,
    'news_from_date' => 'text',
    'news_to_date' => 'text',
    'status' => -1,
    'url_key' => 'test',
    'visibility' => -1,
    'price' => -10.11,
    'special_price' => -10.11,
    'special_from_date' => 'Text\'',
    'special_to_date' => 'Text\'',
    'group_price' => array(
        array('website_id' => 0, 'cust_group' => -1, 'price' => 11),
        array('website_id' => 0, 'cust_group' => 1, 'price' => -11),
        array('website_id' => -2, 'cust_group' => 1, 'price' => 11),
        array('website_id' => '', 'price' => 11),
        array('cust_group' => '', 'price' => 11),
        array('website_id' => -2, 'cust_group' => 1)
    ),
    'tier_price' => array(
        array('website_id' => 0, 'cust_group' => -1, 'price_qty' => 5, 'price' => 11),
        array('website_id' => 0, 'cust_group' => 1, 'price_qty' => -5, 'price' => 11),
        array('website_id' => 0, 'cust_group' => 1, 'price_qty' => 'text', 'price' => 11),
        array('website_id' => 0, 'cust_group' => 1, 'price_qty' => 5, 'price' => -11),
        array('website_id' => -1, 'cust_group' => 1, 'price_qty' => 5, 'price' => 11),
        array('website_id' => '', 'cust_group' => 1, 'price_qty' => 5, 'price' => 11),
        array('website_id' => 0, 'cust_group' => '', 'price_qty' => 5, 'price' => 11),
        array('website_id' => 0, 'cust_group' => 1, 'price' => 0),
        array('website_id' => 0, 'cust_group' => 1,  'price_qty' => 5, 'price' => '')
    ),
    'msrp_enabled' => -1,
    'msrp_display_actual_price_type' => -1,
    'msrp' => -10.11,
    'enable_googlecheckout' => -1,
    'tax_class_id' => -1,
    'meta_title' => 'Test \' title',
    'meta_keyword' => 'Test \' keyword',
    'meta_description' => str_pad('', 100, 'a4b'),
    'custom_design' => 'test',
    'custom_design_from' => 'Text\'',
    'custom_design_to' => 'Text\'',
    'custom_layout_update' => 'Test Custom Layout Update',
    'page_layout' => 'Text\'',
    'options_container' => 'Text\'',
    'gift_message_available' => -1,
    'gift_wrapping_available' => -1,
    'gift_wrapping_price' => -1,
    'stock_data' => array(
        'manage_stock' => 1,
        'qty' => 'Text\'',
        'min_qty' => -1,
        'min_sale_qty' => -1,
        'max_sale_qty' => -1,
        'is_qty_decimal' => 1,
        'is_decimal_divided' => -1,
        'backorders' => -10,
        'notify_stock_qty' => 'text',
        'enable_qty_increments' => 1,
        'qty_increments' => -1,
        'is_in_stock' => -1
    )
);
