<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;

$attributeCodes = array(
    'name',
    'description',
    'short_description',
    'sku',
    'price',
    'status',
    'visibility',
    'tax_class_id',
    'weight'
);

$entityTypeId = $installer->getEntityTypeId(Mage_Catalog_Model_Product::ENTITY);

$minimalAttributeSetName = 'Minimal';
$installer->addAttributeSet($entityTypeId, $minimalAttributeSetName);
$setId = $installer->getAttributeSetId($entityTypeId, $minimalAttributeSetName);

foreach ($attributeCodes as $attributeCode) {
    $attribute = $installer->getAttribute('catalog_product', $attributeCode);
    $installer->addAttributeToSet($entityTypeId, $setId, $installer->getGeneralGroupName(), $attribute['attribute_id']);
}
