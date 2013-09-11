<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * This fixture is run outside of the transaction because it performs DDL operations during creating custom attribute.
 * All the changes are reverted in the appropriate "rollback" fixture.
 */

/** @var $connection Magento_TestFramework_Db_Adapter_TransactionInterface */
$connection = Mage::getSingleton('Magento\Core\Model\Resource')->getConnection('write');
$connection->commitTransparentTransaction();

$entityType = Mage::getModel('\Magento\Eav\Model\Config')->getEntityType('customer_address');
/** @var $entityType \Magento\Eav\Model\Entity\Type */

$attributeSet = Mage::getModel('\Magento\Eav\Model\Entity\Attribute\Set');
/** @var $attributeSet \Magento\Eav\Model\Entity\Attribute\Set */

$attribute = Mage::getModel('\Magento\Customer\Model\Attribute',
    array(
        'data' => array(
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
        )
    )
);
$attribute->setAttributeCode('fixture_address_attribute');
$attribute->save();

$addressData = include(__DIR__ . '/../../../Magento/Sales/_files/address_data.php');
$billingAddress = Mage::getModel('\Magento\Sales\Model\Order\Address', array('data' => $addressData));
$billingAddress->setAddressType('billing');
$billingAddress->setData($attribute->getAttributeCode(), 'fixture_attribute_custom_value');
$billingAddress->save();

$connection->beginTransparentTransaction();
