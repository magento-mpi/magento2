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

/** @var $installer \Magento\Catalog\Model\Resource\Setup */
$installer = \Mage::getResourceModel('Magento\Catalog\Model\Resource\Setup', array('resourceName' => 'catalog_setup'));
$attributeSetId = $installer->getAttributeSetId('catalog_product', 'Default');
$entityModel = \Mage::getModel('Magento\Eav\Model\Entity');
$entityTypeId = $entityModel->setType(\Magento\Catalog\Model\Product::ENTITY)->getTypeId();
$groupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

/** @var $attribute \Magento\Catalog\Model\Resource\Eav\Attribute */
$attribute = \Mage::getResourceModel('Magento\Catalog\Model\Resource\Eav\Attribute');
$attribute->setAttributeCode('filterable_attribute_a')
    ->setEntityTypeId($entityTypeId)
    ->setAttributeGroupId($groupId)
    ->setAttributeSetId($attributeSetId)
    ->setIsFilterable(1)
    ->setIsUserDefined(1)
    ->save();

$attribute = \Mage::getResourceModel('Magento\Catalog\Model\Resource\Eav\Attribute');
$attribute->setAttributeCode('filterable_attribute_b')
    ->setEntityTypeId($entityTypeId)
    ->setAttributeGroupId($groupId)
    ->setAttributeSetId($attributeSetId)
    ->setIsFilterable(1)
    ->setIsUserDefined(1)
    ->save();
