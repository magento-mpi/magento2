<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this Mage_Catalog_Model_Resource_Setup */

$attributeCodes = array(
    'price_type',
    'price_view',
);

$entityTypeId = $this->getEntityTypeId(Mage_Catalog_Model_Product::ENTITY);

$minimalAttributeSetName = 'Minimal';
$this->addAttributeSet($entityTypeId, $minimalAttributeSetName);
$setId = $this->getAttributeSetId($entityTypeId, $minimalAttributeSetName);

foreach ($attributeCodes as $attributeCode) {
    $attribute = $this->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
    $this->addAttributeToSet($entityTypeId, $setId, $this->getGeneralGroupName(), $attribute['attribute_id']);
}
