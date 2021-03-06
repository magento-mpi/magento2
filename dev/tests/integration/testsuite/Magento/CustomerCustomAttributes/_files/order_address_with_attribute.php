<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * This fixture is run outside of the transaction because it performs DDL operations during creating custom attribute.
 * All the changes are reverted in the appropriate "rollback" fixture.
 */

/** @var $connection \Magento\TestFramework\Db\Adapter\TransactionInterface */
$connection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
    'Magento\Framework\App\Resource'
)->getConnection(
    'core_write'
);
$connection->commitTransparentTransaction();

$entityType = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Eav\Model\Config'
)->getEntityType(
    'customer_address'
);
/** @var $entityType \Magento\Eav\Model\Entity\Type */

$attributeSet = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Eav\Model\Entity\Attribute\Set'
);
/** @var $attributeSet \Magento\Eav\Model\Entity\Attribute\Set */

$attribute = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Customer\Model\Attribute',
    [
        'data' => [
            'frontend_input' => 'text',
            'frontend_label' => ['Fixture Customer Address Attribute'],
            'sort_order' => '0',
            'backend_type' => 'varchar',
            'is_user_defined' => 1,
            'is_system' => 0,
            'is_required' => '0',
            'is_visible' => '0',
            'attribute_set_id' => $entityType->getDefaultAttributeSetId(),
            'attribute_group_id' => $attributeSet->getDefaultGroupId($entityType->getDefaultAttributeSetId()),
            'entity_type_id' => $entityType->getId(),
            'default_value' => 'fixture_attribute_default_value',
        ]
    ]
);
$attribute->setAttributeCode('fixture_address_attribute');
$attribute->save();

$addressData = include __DIR__ . '/../../../Magento/Sales/_files/address_data.php';
$billingAddress = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Sales\Model\Order\Address',
    ['data' => $addressData]
);
$billingAddress->setAddressType('billing');
$billingAddress->setData($attribute->getAttributeCode(), 'fixture_attribute_custom_value');
$billingAddress->save();

$connection->beginTransparentTransaction();
