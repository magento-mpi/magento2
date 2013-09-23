<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Weee
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Catalog_Model_Resource_Setup', array('resourceName' => 'catalog_setup'));
$attributeSetId = $installer->getAttributeSetId('catalog_product', 'Default');
$entityModel = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Eav_Model_Entity');
$entityTypeId = $entityModel->setType(Magento_Catalog_Model_Product::ENTITY)->getTypeId();
$groupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$attribute = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Catalog_Model_Resource_Eav_Attribute');
$attribute->setAttributeCode('fpt_for_all')
    ->setEntityTypeId($entityTypeId)
    ->setAttributeGroupId($groupId)
    ->setAttributeSetId($attributeSetId)
    ->setFrontendInput('weee')
    ->setIsUserDefined(1)
    ->save();

$product = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Model_Product');
$product->setTypeId('simple')
    ->setId(1)
    ->setAttributeSetId($attributeSetId)
    ->setStoreId(1)
    ->setWebsiteIds(array(1))
    ->setName('Simple Product')
    ->setSku('simple')
    ->setPrice(100)
    ->setFptForAll(array(array('website_id' => 0, 'country' => 'US', 'state' => 0, 'price' => 0.07, 'delete' => '')))
    ->save();
