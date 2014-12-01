<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
/** @var \Magento\Eav\Model\Entity\Attribute\Set $attributeSet */
$attributeSet = $objectManager->create('Magento\Eav\Model\Entity\Attribute\Set');
$entityTypeId = $objectManager->create('Magento\Eav\Model\Entity\Type')->loadByCode('catalog_product')->getId();
$attributeSet->setData(array(
    'attribute_set_name' => 'empty_attribute_set',
    'entity_type_id' => $entityTypeId,
    'sort_order' => 200,
));
$attributeSet->validate();
$attributeSet->save();
