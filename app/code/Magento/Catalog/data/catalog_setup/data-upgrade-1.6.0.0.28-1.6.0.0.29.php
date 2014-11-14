<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\Catalog\Model\Resource\Setup */
$oldTabName = 'Search Optimization';
$newTabName = 'Search Engine Optimization';
$entityTypeId = $this->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
$attributeSetId = $this->getAttributeSetId($entityTypeId, 'Default');
$groupId = $this->getAttributeGroupId($entityTypeId, $attributeSetId, $oldTabName);

$this->updateAttributeGroup($entityTypeId, $attributeSetId, $groupId, 'attribute_group_name', $newTabName);
$this->updateAttributeGroup(
    $entityTypeId,
    $attributeSetId,
    $groupId,
    'attribute_group_code',
    preg_replace('/[^a-z0-9]+/', '-', strtolower($newTabName))
);
