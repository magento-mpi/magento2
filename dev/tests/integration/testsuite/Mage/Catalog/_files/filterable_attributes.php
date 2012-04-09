<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = new Mage_Catalog_Model_Resource_Setup('catalog_setup');
$attributeSetId = $installer->getAttributeSetId('catalog_product', 'Default');
$entityModel = new Mage_Eav_Model_Entity;
$entityTypeId = $entityModel->setType(Mage_Catalog_Model_Product::ENTITY)->getTypeId();
$groupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$attribute = new Mage_Catalog_Model_Resource_Eav_Attribute;
$attribute->setAttributeCode('filterable_attribute_a')
    ->setEntityTypeId($entityTypeId)
    ->setAttributeGroupId($groupId)
    ->setAttributeSetId($attributeSetId)
    ->setIsFilterable(1)
    ->setIsUserDefined(1)
    ->save();

$attribute = new Mage_Catalog_Model_Resource_Eav_Attribute;
$attribute->setAttributeCode('filterable_attribute_b')
    ->setEntityTypeId($entityTypeId)
    ->setAttributeGroupId($groupId)
    ->setAttributeSetId($attributeSetId)
    ->setIsFilterable(1)
    ->setIsUserDefined(1)
    ->save();
