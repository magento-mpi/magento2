<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Insert system ACL roles (customer and guest) along with ACL rules for them.
 * These roles should be blocked for any modifications from the admin panel.
 */

/** @var $installer \Magento\Framework\Module\Setup */
$installer = $this;
$installer->startSetup();

$roleTable = $installer->getTable('admin_role');
$ruleTable = $installer->getTable('admin_rule');

$installer->getConnection()->insert(
    $roleTable,
    [
        'role_type' => \Magento\User\Model\Acl\Role\User::ROLE_TYPE,
        'role_name' => 'Customer',
        'user_type' => \Magento\Authz\Model\UserIdentifier::USER_TYPE_CUSTOMER,
    ]
);
$customerRoleId = $installer->getConnection()->lastInsertId($roleTable);
$installer->getConnection()->insert(
    $ruleTable,
    [
        'role_id' => $customerRoleId,
        'resource_id' => 'self',
        'permission' => 'allow',
    ]
);

$installer->getConnection()->insert(
    $roleTable,
    [
        'role_type' => \Magento\User\Model\Acl\Role\User::ROLE_TYPE,
        'role_name' => 'Guest',
        'user_type' => \Magento\Authz\Model\UserIdentifier::USER_TYPE_GUEST,
    ]
);
$guestRoleId = $installer->getConnection()->lastInsertId($roleTable);
$installer->getConnection()->insert(
    $ruleTable,
    [
        'role_id' => $guestRoleId,
        'resource_id' => 'anonymous',
        'permission' => 'allow',
    ]
);

$installer->endSetup();
