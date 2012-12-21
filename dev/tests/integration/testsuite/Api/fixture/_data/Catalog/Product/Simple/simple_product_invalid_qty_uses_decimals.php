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
    'name' => 'Test Name',
    'description' => 'Test Description',
    'short_description' => 'Test Short Description',
    'sku' => 'simple' . uniqid(),
    'weight' => 10.11,
    'status' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED,
    'visibility' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
    'price' => 1,
    'tax_class_id' => $taxClass['class_id'],
    'stock_data' => array(
        'manage_stock' => 1,
        'qty' => 1,
        'is_qty_decimal' => -1,
        'enable_qty_increments' => 1
    )
);

