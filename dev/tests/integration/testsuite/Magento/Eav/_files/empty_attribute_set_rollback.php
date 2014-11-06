<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
/** @var \Magento\Eav\Model\Entity\Attribute\Set $attributeSet */
$attributeSet = $objectManager->create('Magento\Eav\Model\Entity\Attribute\Set')
    ->load('empty_attribute_set', 'attribute_set_name');
if ($attributeSet->getId()) {
    $attributeSet->delete();
}
