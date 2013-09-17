<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftMessage
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this Magento_Catalog_Model_Resource_Setup */

$groupName = 'Autosettings';
$entityTypeId = $this->getEntityTypeId(Magento_Catalog_Model_Product::ENTITY);
$attributeSetId = $this->getAttributeSetId($entityTypeId, 'Default');

$attribute = $this->getAttribute($entityTypeId, 'gift_message_available');
if ($attribute) {
    $this->addAttributeToGroup($entityTypeId, $attributeSetId, $groupName, $attribute['attribute_id'], 60);
}

if (!$this->getAttributesNumberInGroup($entityTypeId, $attributeSetId, 'Gift Options')) {
    $this->removeAttributeGroup($entityTypeId, $attributeSetId, 'Gift Options');
}
