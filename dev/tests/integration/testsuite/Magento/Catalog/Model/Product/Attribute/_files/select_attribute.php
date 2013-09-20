<?php
/**
 * "dropdown" fixture of product EAV attribute.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var \Magento\Eav\Model\Entity\Type $entityType */
$entityType = \Mage::getModel('Magento\Eav\Model\Entity\Type');
$entityType->loadByCode('catalog_product');
$defaultSetId = $entityType->getDefaultAttributeSetId();
/** @var \Magento\Eav\Model\Entity\Attribute\Set $defaultSet */
$defaultSet = \Mage::getModel('Magento\Eav\Model\Entity\Attribute\Set');
$defaultSet->load($defaultSetId);
$defaultGroupId = $defaultSet->getDefaultGroupId();
$optionData = array(
    'value' => array(
        'option_1' => array(0 => 'Fixture Option'),
    ),
    'order' => array(
        'option_1' => 1,
    )
);

/** @var $attribute \Magento\Catalog\Model\Resource\Eav\Attribute */
$attribute = \Mage::getResourceModel('Magento\Catalog\Model\Resource\Eav\Attribute');
$attribute->setAttributeCode('select_attribute')
    ->setEntityTypeId($entityType->getEntityTypeId())
    ->setAttributeGroupId($defaultGroupId)
    ->setAttributeSetId($defaultSetId)
    ->setFrontendInput('select')
    ->setFrontendLabel('Select Attribute')
    ->setBackendType('int')
    ->setIsUserDefined(1)
    ->setOption($optionData)
    ->save();
