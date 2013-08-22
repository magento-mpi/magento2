<?php
/**
 * "dropdown" fixture of product EAV attribute.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var Magento_Eav_Model_Entity_Type $entityType */
$entityType = Mage::getModel('Magento_Eav_Model_Entity_Type');
$entityType->loadByCode('catalog_product');
$defaultSetId = $entityType->getDefaultAttributeSetId();
/** @var Magento_Eav_Model_Entity_Attribute_Set $defaultSet */
$defaultSet = Mage::getModel('Magento_Eav_Model_Entity_Attribute_Set');
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

/** @var $attribute Magento_Catalog_Model_Resource_Eav_Attribute */
$attribute = Mage::getResourceModel('Magento_Catalog_Model_Resource_Eav_Attribute');
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
