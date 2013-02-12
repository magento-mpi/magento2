<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GiftMessage
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this Mage_Catalog_Model_Resource_Setup */

$groupName = 'Autosettings';
$entityTypeId = $this->getEntityTypeId(Mage_Catalog_Model_Product::ENTITY);
$attributeSetId = $this->getAttributeSetId($entityTypeId, 'Default');

$attribute = $this->getAttribute($entityTypeId, 'gift_message_available');
if ($attribute) {
    $this->addAttributeToGroup($entityTypeId, $attributeSetId, $groupName, $attribute['attribute_id'], 60);
}
