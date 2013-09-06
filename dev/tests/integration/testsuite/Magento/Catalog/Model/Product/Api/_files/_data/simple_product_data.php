<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

/** @var $entityType Magento_Eav_Model_Entity_Type */
$entityType = Mage::getModel('Magento_Eav_Model_Entity_Type')->loadByCode('catalog_product');
$taxClasses = Mage::getResourceModel('Magento_Tax_Model_Resource_TaxClass_Collection')->toArray();
$taxClass = reset($taxClasses['items']);

return array(
    'type_id' => Magento_Catalog_Model_Product_Type::TYPE_SIMPLE,
    'attribute_set_id' => $entityType->getDefaultAttributeSetId(),
    'sku' => 'simple' . uniqid(),
    'weight' => 1,
    'status' => Magento_Catalog_Model_Product_Status::STATUS_ENABLED,
    'visibility' => Magento_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
    'name' => 'Simple Product',
    'description' => 'Simple Description',
    'short_description' => 'Simple Short Description',
    'price' => 99.95,
    'tax_class_id' => $taxClass['class_id'],
);
