<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Weee
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = new Mage_Catalog_Model_Resource_Setup('catalog_setup');
$attributeSetId = $installer->getAttributeSetId('catalog_product', 'Default');
$entityModel = new Mage_Eav_Model_Entity();
$entityTypeId = $entityModel->setType(Mage_Catalog_Model_Product::ENTITY)->getTypeId();
$groupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$attribute = new Mage_Catalog_Model_Resource_Eav_Attribute();
$attribute->setAttributeCode('fpt_for_all')
    ->setEntityTypeId($entityTypeId)
    ->setAttributeGroupId($groupId)
    ->setAttributeSetId($attributeSetId)
    ->setFrontendInput('weee')
    ->setIsUserDefined(1)
    ->save();

$product = new Mage_Catalog_Model_Product();
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
