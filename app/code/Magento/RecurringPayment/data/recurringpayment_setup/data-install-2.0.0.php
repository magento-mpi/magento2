<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\RecurringPayment\Model\Resource\Setup */
$this->installEntities();
$entityTypeId = $this->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
$attributeSetId = $this->getAttributeSetId($entityTypeId, 'Default');

$groupName = 'Recurring Payment';
$this->updateAttributeGroup($entityTypeId, $attributeSetId, $groupName, 'sort_order', 41);
$this->updateAttributeGroup($entityTypeId, $attributeSetId, $groupName, 'attribute_group_code', 'recurring-payment');
$this->updateAttributeGroup($entityTypeId, $attributeSetId, $groupName, 'tab_group_code', 'advanced');

$this->addAttributeToGroup($entityTypeId, $attributeSetId, $groupName, 'is_recurring');
$this->addAttributeToGroup($entityTypeId, $attributeSetId, $groupName, 'recurring_payment');

$connection = $this->getConnection();
$adminRuleTable = $this->getTable('authorization_rule');
$connection->update(
    $adminRuleTable,
    array('resource_id' => 'Magento_RecurringPayment::recurring_payment'),
    array('resource_id = ?' => 'Magento_Sales::recurring_payment')
);
