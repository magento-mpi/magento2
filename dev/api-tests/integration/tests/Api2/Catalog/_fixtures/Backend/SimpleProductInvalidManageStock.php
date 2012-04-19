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
$entityType = Mage::getModel('eav/entity_type')->loadByCode('catalog_product');
$taxClasses = Mage::getResourceModel('tax/class_collection')->toArray();
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
        'manage_stock' => 'Text',
        'qty' => 1,
    )
);

