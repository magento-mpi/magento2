<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
/** @var \Magento\Eav\Model\Entity\Attribute\Group $attributeSet */
$attributeGroup = $objectManager->create('\Magento\Eav\Model\Entity\Attribute\Group')
    ->load('empty_attribute_group', 'attribute_group_name');
if ($attributeGroup->getId()) {
    $attributeGroup->delete();
}

$attributeGroupUpdated = $objectManager->create('\Magento\Eav\Model\Entity\Attribute\Group')
    ->load('empty_attribute_group_updated', 'attribute_group_name');
if ($attributeGroupUpdated->getId()) {
    $attributeGroupUpdated->delete();
}
