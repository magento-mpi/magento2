<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this Mage_Catalog_Model_Resource_Setup */
$idField = 'attribute_group_id';
$attributeGroupId = (int)$this->getTableRow('eav_attribute_group', 'attribute_group_name', 'Recurring Profile',
    $idField, 'attribute_group_code', 'recurring-profile');

if ($attributeGroupId) {
    $this->deleteTableRow('eav_attribute_group', $idField, $attributeGroupId);
}

$entityTypeId = $this->getEntityTypeId(Mage_Catalog_Model_Product::ENTITY);
$this->removeAttribute($entityTypeId, 'is_recurring');
$this->removeAttribute($entityTypeId, 'recurring_profile');
