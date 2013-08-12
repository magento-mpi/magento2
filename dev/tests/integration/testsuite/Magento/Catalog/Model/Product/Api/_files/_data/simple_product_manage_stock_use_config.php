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
    'sku' => 'simple' . uniqid(),
    'name' => 'Test',
    'description' => 'Test description',
    'short_description' => 'Test short description',
    'weight' => 125,
    'status' => Magento_Catalog_Model_Product_Status::STATUS_ENABLED,
    'visibility' => Magento_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
    'price' => 25.50,
    'tax_class_id' => $taxClass['class_id'],
    // Field should not be validated if "Use Config Settings" checkbox is set
    // thus invalid value should not raise error
    'stock_data' => array(
        'manage_stock' => -1,
        'use_config_manage_stock' => 1,
        'qty' => 1,
        'min_qty' => -1,
        'min_sale_qty' => -1,
        'use_config_min_sale_qty' => -1,
        'max_sale_qty' => -1,
        'use_config_max_sale_qty' => -1,
        'is_qty_decimal' => -1,
        'backorders' => -1,
        'notify_stock_qty' => 'text',
        'enable_qty_increments' => -100,
        'is_in_stock' => -1
    )
);
