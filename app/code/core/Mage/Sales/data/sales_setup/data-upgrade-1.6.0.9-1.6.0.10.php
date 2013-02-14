<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this Mage_Catalog_Model_Resource_Setup */

$groupName = 'Advanced Pricing';
$entityTypeId = $this->getEntityTypeId(Mage_Catalog_Model_Product::ENTITY);
$attributeSetId = $this->getAttributeSetId($entityTypeId, 'Default');

$attributesOrder = array(
    'is_recurring' => 30,
    'recurring_profile' => 40,
);

foreach ($attributesOrder as $key => $value) {
    $attribute = $this->getAttribute($entityTypeId, $key);
    if ($attribute) {
        $this->addAttributeToGroup($entityTypeId, $attributeSetId, $groupName, $attribute['attribute_id'], $value);
    }
}

$this->removeAttributeGroup($entityTypeId, $attributeSetId, 'Recurring Profile');
