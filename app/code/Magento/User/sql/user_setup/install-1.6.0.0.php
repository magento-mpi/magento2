<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/* @var $installer Magento_Core_Model_Resource_Setup */

$installer->startSetup();

/**
 * Create table 'admin_assert'
 */
if (!$installer->getConnection()->isTableExists($installer->getTable('admin_assert'))) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('admin_assert'))
        ->addColumn('assert_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            ), 'Assert ID')
        ->addColumn('assert_type', Magento_DB_Ddl_Table::TYPE_TEXT, 20, array(
            'nullable'  => true,
            'default'   => null,
            ), 'Assert Type')
        ->addColumn('assert_data', Magento_DB_Ddl_Table::TYPE_TEXT, '64k', array(
            ), 'Assert Data')
        ->setComment('Admin Assert Table');
    $installer->getConnection()->createTable($table);
}

/**
 * Create table 'admin_role'
 */
if (!$installer->getConnection()->isTableExists($installer->getTable('admin_role'))) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('admin_role'))
        ->addColumn('role_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            ), 'Role ID')
        ->addColumn('parent_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
            ), 'Parent Role ID')
        ->addColumn('tree_level', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
            ), 'Role Tree Level')
        ->addColumn('sort_order', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
            ), 'Role Sort Order')
        ->addColumn('role_type', Magento_DB_Ddl_Table::TYPE_TEXT, 1, array(
            'nullable'  => false,
            'default'   => '0',
            ), 'Role Type')
        ->addColumn('user_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
            ), 'User ID')
        ->addColumn('role_name', Magento_DB_Ddl_Table::TYPE_TEXT, 50, array(
            'nullable'  => true,
            'default'   => null,
            ), 'Role Name')
        ->addIndex($installer->getIdxName('admin_role', array('parent_id', 'sort_order')),
            array('parent_id', 'sort_order'))
        ->addIndex($installer->getIdxName('admin_role', array('tree_level')),
            array('tree_level'))
        ->setComment('Admin Role Table');
    $installer->getConnection()->createTable($table);
}
/**
 * Create table 'admin_rule'
 */
if (!$installer->getConnection()->isTableExists($installer->getTable('admin_rule'))) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('admin_rule'))
        ->addColumn('rule_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            ), 'Rule ID')
        ->addColumn('role_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
            ), 'Role ID')
        ->addColumn('resource_id', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable'  => true,
            'default'   => null,
            ), 'Resource ID')
        ->addColumn('privileges', Magento_DB_Ddl_Table::TYPE_TEXT, 20, array(
            'nullable'  => true,
            ), 'Privileges')
        ->addColumn('assert_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
            ), 'Assert ID')
        ->addColumn('role_type', Magento_DB_Ddl_Table::TYPE_TEXT, 1, array(
            ), 'Role Type')
        ->addColumn('permission', Magento_DB_Ddl_Table::TYPE_TEXT, 10, array(
            ), 'Permission')
        ->addIndex($installer->getIdxName('admin_rule', array('resource_id', 'role_id')),
            array('resource_id', 'role_id'))
        ->addIndex($installer->getIdxName('admin_rule', array('role_id', 'resource_id')),
            array('role_id', 'resource_id'))
        ->addForeignKey($installer->getFkName('admin_rule', 'role_id', 'admin_role', 'role_id'),
            'role_id', $installer->getTable('admin_role'), 'role_id',
            Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
        ->setComment('Admin Rule Table');
    $installer->getConnection()->createTable($table);
}

/**
 * Create table 'admin_user'
 */
if (!$installer->getConnection()->isTableExists($installer->getTable('admin_user'))) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('admin_user'))
        ->addColumn('user_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            ), 'User ID')
        ->addColumn('firstname', Magento_DB_Ddl_Table::TYPE_TEXT, 32, array(
            'nullable'  => true,
            ), 'User First Name')
        ->addColumn('lastname', Magento_DB_Ddl_Table::TYPE_TEXT, 32, array(
            'nullable'  => true,
            ), 'User Last Name')
        ->addColumn('email', Magento_DB_Ddl_Table::TYPE_TEXT, 128, array(
            'nullable'  => true,
            ), 'User Email')
        ->addColumn('username', Magento_DB_Ddl_Table::TYPE_TEXT, 40, array(
            'nullable'  => true,
            ), 'User Login')
        ->addColumn('password', Magento_DB_Ddl_Table::TYPE_TEXT, 40, array(
            'nullable'  => true,
            ), 'User Password')
        ->addColumn('created', Magento_DB_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable'  => false,
            ), 'User Created Time')
        ->addColumn('modified', Magento_DB_Ddl_Table::TYPE_TIMESTAMP, null, array(
            ), 'User Modified Time')
        ->addColumn('logdate', Magento_DB_Ddl_Table::TYPE_TIMESTAMP, null, array(
            ), 'User Last Login Time')
        ->addColumn('lognum', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
            ), 'User Login Number')
        ->addColumn('reload_acl_flag', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
            'nullable'  => false,
            'default'   => '0',
            ), 'Reload ACL')
        ->addColumn('is_active', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
            'nullable'  => false,
            'default'   => '1',
            ), 'User Is Active')
        ->addColumn('extra', Magento_DB_Ddl_Table::TYPE_TEXT, '64k', array(
            ), 'User Extra Data')
        ->addIndex(
            $installer->getIdxName('admin_user', array('username'), Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE),
            array('username'),
            array('type' => Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE)
        )
        ->setComment('Admin User Table');
    $installer->getConnection()->createTable($table);
}
$installer->endSetup();
