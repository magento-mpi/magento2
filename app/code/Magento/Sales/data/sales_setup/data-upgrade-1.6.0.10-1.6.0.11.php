<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\Catalog\Model\Resource\Setup */

$entityTypeId = $this->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
$attributeSetId = $this->getAttributeSetId($entityTypeId, 'Default');

$groupName = 'Recurring Profile';
$this->updateAttributeGroup($entityTypeId, $attributeSetId, $groupName, 'sort_order', 41);
$this->updateAttributeGroup($entityTypeId, $attributeSetId, $groupName, 'attribute_group_code', 'recurring-profile');
$this->updateAttributeGroup($entityTypeId, $attributeSetId, $groupName, 'tab_group_code', 'advanced');

$this->addAttributeToGroup($entityTypeId, $attributeSetId, $groupName, 'is_recurring');
$this->addAttributeToGroup($entityTypeId, $attributeSetId, $groupName, 'recurring_profile');
