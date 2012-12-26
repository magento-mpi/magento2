<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var $entityType Mage_Eav_Model_Entity_Type */
$entityType = Mage::getModel('Mage_Eav_Model_Entity_Type')->loadByCode('catalog_product');

$attributeSet = Mage::getModel('Mage_Eav_Model_Entity_Attribute_Set');
$attributeSet->setEntityTypeId($entityType->getEntityTypeId())
    ->setAttributeSetName('Test Attribute Set ' . uniqid());

return $attributeSet;
