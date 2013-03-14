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

$attributeCodes = array(
    'allow_open_amount',
    'giftcard_amounts',
    'giftcard_type'
);

$entityTypeId = $this->getEntityTypeId(Mage_Catalog_Model_Product::ENTITY);

$setId = $this->getAttributeSetId($entityTypeId, 'Minimal');

foreach ($attributeCodes as $attributeCode) {
    $attribute = $this->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
    $this->addAttributeToSet($entityTypeId, $setId, $this->getGeneralGroupName(), $attribute['attribute_id']);
}
