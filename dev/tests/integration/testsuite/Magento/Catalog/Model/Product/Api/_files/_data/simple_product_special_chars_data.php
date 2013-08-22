<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

/** @var $entityType Magento_Eav_Model_Entity_Type */
$entityType = Mage::getModel('Magento_Eav_Model_Entity_Type')->loadByCode('catalog_product');
$taxClasses = Mage::getResourceModel('Magento_Tax_Model_Resource_Class_Collection')->toArray();
$taxClass = reset($taxClasses['items']);

return array(
    'type_id' => Magento_Catalog_Model_Product_Type::TYPE_SIMPLE,
    'attribute_set_id' => $entityType->getDefaultAttributeSetId(),
    'name' => '!";%:?*()_+{}[]\|<>,.?/',
    'description' => '!";%:?*()_+{}[]\|<>,.?/',
    'short_description' => '!";%:?*()_+{}[]\|<>,.?/',
    'sku' => '!";%:?*()_+{}[]\|<>,.?/' . uniqid(),
    'weight' => 125,
    'news_from_date' => '02/16/2012',
    'news_to_date' => '16.02.2012',
    'status' => Magento_Catalog_Model_Product_Status::STATUS_ENABLED,
    'url_key' => '123!";%:?*()_+{}[]\|<>,.?/abc',
    'visibility' => Magento_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
    'price' => 25.50,
    'special_price' => 11.2,
    'special_from_date' => '02/16/2012',
    'special_to_date' => '03/17/2012',
    'group_price' => array(
        array('website_id' => 0, 'cust_group' => 1, 'price' => 11)
    ),
    'tier_price' => array(
        array('website_id' => 0, 'cust_group' => 1, 'price_qty' => 5.5, 'price' => 11.054)
    ),
    'msrp_enabled' => 1,
    'msrp_display_actual_price_type' => 1,
    'msrp' => 11.015,
    'enable_googlecheckout' => 1,
    'tax_class_id' => $taxClass['class_id'],
    'meta_title' => '!";%:?*()_+{}[]\|<>,.?/',
    'meta_keyword' => '!";%:?*()_+{}[]\|<>,.?/',
    'meta_description' => str_pad('', 8, '!";%:?*_+{}[]\|<>,.?'),
    'custom_design' => 'magento_blank',
    'custom_design_from' => date('Y-m-d'),
    'custom_design_to' => date('Y-m-d', time() + 24 * 3600),
    'page_layout' => 'one_column',
);
