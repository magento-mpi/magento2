<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this Magento_Catalog_Model_Resource_Setup */

$groupName = 'Autosettings';
$entityTypeId = $this->getEntityTypeId(Magento_Catalog_Model_Product::ENTITY);
$attributeSetId = $this->getAttributeSetId($entityTypeId, 'Default');

$attributesOrder = array(
    'gift_wrapping_available' => 70,
    'gift_wrapping_price' => 80,
);

foreach ($attributesOrder as $key => $value) {
    $attribute = $this->getAttribute($entityTypeId, $key);
    if ($attribute) {
        $this->addAttributeToGroup($entityTypeId, $attributeSetId, $groupName, $attribute['attribute_id'], $value);
    }
}

if (!$this->getAttributesNumberInGroup($entityTypeId, $attributeSetId, 'Gift Options')) {
    $this->removeAttributeGroup($entityTypeId, $attributeSetId, 'Gift Options');
}
