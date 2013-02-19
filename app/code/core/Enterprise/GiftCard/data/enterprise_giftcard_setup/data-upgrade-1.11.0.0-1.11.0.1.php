<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this Mage_Catalog_Model_Resource_Setup */

$groupName = 'Product Details';
$entityTypeId = $this->getEntityTypeId(Mage_Catalog_Model_Product::ENTITY);
$attributeSetId = $this->getAttributeSetId($entityTypeId, 'Default');

$attributesOrder = array(
    'giftcard_type' => 31,
    'giftcard_amounts' => 32,
    'allow_open_amount' => 33,
    'open_amount_min' => 34,
    'open_amount_max' => 35,
);

foreach ($attributesOrder as $key => $order) {
    $attribute = $this->getAttribute($entityTypeId, $key);
    if ($attribute) {
        $this->addAttributeToGroup($entityTypeId, $attributeSetId, $groupName, $attribute['attribute_id'], $order);
    }
}

$attributeProperties = array(
    'giftcard_type' => array('is_visible' => 1),
    'allow_open_amount' => array('is_required' => 0)
);

foreach ($attributeProperties as $attributeName => $properties) {
    $attribute = $this->getAttribute($entityTypeId, $attributeName);
    if ($attribute) {
        foreach ($properties as $propertyName => $propertyValue) {
            $this->updateAttribute($entityTypeId, $attribute['attribute_id'], $propertyName, $propertyValue);
        }
    }
}
