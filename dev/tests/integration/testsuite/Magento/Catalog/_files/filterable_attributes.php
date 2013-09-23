<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Catalog_Model_Resource_Setup */
$installer = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Catalog_Model_Resource_Setup', array('resourceName' => 'catalog_setup'));
$attributeSetId = $installer->getAttributeSetId('catalog_product', 'Default');
$entityModel = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Eav_Model_Entity');
$entityTypeId = $entityModel->setType(Magento_Catalog_Model_Product::ENTITY)->getTypeId();
$groupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

/** @var $attribute Magento_Catalog_Model_Resource_Eav_Attribute */
$attribute = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Catalog_Model_Resource_Eav_Attribute');
$attribute->setAttributeCode('filterable_attribute_a')
    ->setEntityTypeId($entityTypeId)
    ->setAttributeGroupId($groupId)
    ->setAttributeSetId($attributeSetId)
    ->setIsFilterable(1)
    ->setIsUserDefined(1)
    ->save();

$attribute = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Catalog_Model_Resource_Eav_Attribute');
$attribute->setAttributeCode('filterable_attribute_b')
    ->setEntityTypeId($entityTypeId)
    ->setAttributeGroupId($groupId)
    ->setAttributeSetId($attributeSetId)
    ->setIsFilterable(1)
    ->setIsUserDefined(1)
    ->save();
