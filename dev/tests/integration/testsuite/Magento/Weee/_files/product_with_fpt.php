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

$installer = Mage::getResourceModel('Magento\Catalog\Model\Resource\Setup', array('resourceName' => 'catalog_setup'));
$attributeSetId = $installer->getAttributeSetId('catalog_product', 'Default');
$entityModel = Mage::getModel('Magento\Eav\Model\Entity');
$entityTypeId = $entityModel->setType(\Magento\Catalog\Model\Product::ENTITY)->getTypeId();
$groupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$attribute = Mage::getResourceModel('Magento\Catalog\Model\Resource\Eav\Attribute');
$attribute->setAttributeCode('fpt_for_all')
    ->setEntityTypeId($entityTypeId)
    ->setAttributeGroupId($groupId)
    ->setAttributeSetId($attributeSetId)
    ->setFrontendInput('weee')
    ->setIsUserDefined(1)
    ->save();

$product = Mage::getModel('Magento\Catalog\Model\Product');
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
