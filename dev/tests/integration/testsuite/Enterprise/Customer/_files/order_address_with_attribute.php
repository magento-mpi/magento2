<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Customer
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$entityType = Mage::getModel('Mage_Eav_Model_Config')->getEntityType('customer_address');
/** @var $entityType Mage_Eav_Model_Entity_Type */

$attributeSet = Mage::getModel('Mage_Eav_Model_Entity_Attribute_Set');
/** @var $attributeSet Mage_Eav_Model_Entity_Attribute_Set */

$attribute = new Mage_Customer_Model_Attribute(array(
    'frontend_input'     => 'text',
    'frontend_label'     => array('Fixture Customer Address Attribute'),
    'sort_order'         => '0',
    'backend_type'       => 'varchar',
    'is_user_defined'    => 1,
    'is_system'          => 0,
    'is_required'        => '0',
    'is_visible'         => '0',
    'attribute_set_id'   => $entityType->getDefaultAttributeSetId(),
    'attribute_group_id' => $attributeSet->getDefaultGroupId($entityType->getDefaultAttributeSetId()),
    'entity_type_id'     => $entityType->getId(),
    'default_value'      => 'fixture_attribute_default_value',
));
$attribute->setAttributeCode('fixture_address_attribute');
$attribute->save();

$addressData = include(__DIR__ . '/../../../Mage/Sales/_files/address_data.php');
$billingAddress = new Mage_Sales_Model_Order_Address($addressData);
$billingAddress->setAddressType('billing');
$billingAddress->setData($attribute->getAttributeCode(), 'fixture_attribute_custom_value');
$billingAddress->save();
