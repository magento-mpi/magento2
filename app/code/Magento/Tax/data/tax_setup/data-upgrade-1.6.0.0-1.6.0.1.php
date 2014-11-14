<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Tax\Model\Resource\Setup */
$installer = $this;

// New attributes order and properties
$properties = array('is_required', 'default_value');
$attributesOrder = array(
    // Product Details tab
    'tax_class_id' => array('Product Details' => 40, 'is_required' => 0, 'default_value' => 2),
);

$entityTypeId = $this->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
$attributeSetId = $this->getAttributeSetId($entityTypeId, 'Default');

foreach ($attributesOrder as $key => $value) {
    $attribute = $installer->getAttribute($entityTypeId, $key);
    if ($attribute) {
        foreach ($value as $propertyName => $propertyValue) {
            if (in_array($propertyName, $properties)) {
                $installer->updateAttribute($entityTypeId, $attribute['attribute_id'], $propertyName, $propertyValue);
            } else {
                $installer->addAttributeToGroup(
                    $entityTypeId,
                    $attributeSetId,
                    $propertyName,
                    $attribute['attribute_id'],
                    $propertyValue
                );
            }
        }
    }
}
