<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Api
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Api install
 *
 * @category    Magento
 * @package     Magento_Api
 * @author      Magento Core Team <core@magentocommerce.com>
 */
$installer = $this;
/* @var $installer \Magento\Core\Model\Resource\Setup */

$installer->startSetup();

/**
 * Create table 'api_assert'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('api_assert'))
    ->addColumn('assert_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Assert id')
    ->addColumn('assert_type', \Magento\DB\Ddl\Table::TYPE_TEXT, 20, array(
        ), 'Assert type')
    ->addColumn('assert_data', \Magento\DB\Ddl\Table::TYPE_TEXT, '64k', array(
        ), 'Assert additional data')
    ->setComment('Api ACL Asserts');
$installer->getConnection()->createTable($table);

/**
 * Create table 'api_role'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('api_role'))
    ->addColumn('role_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Role id')
    ->addColumn('parent_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Parent role id')
    ->addColumn('tree_level', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Role level in tree')
    ->addColumn('sort_order', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Sort order to display on admin area')
    ->addColumn('role_type', \Magento\DB\Ddl\Table::TYPE_TEXT, 1, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Role type')
    ->addColumn('user_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'User id')
    ->addColumn('role_name', \Magento\DB\Ddl\Table::TYPE_TEXT, 50, array(
        ), 'Role name')
    ->addIndex($installer->getIdxName('api_role', array('parent_id', 'sort_order')),
        array('parent_id', 'sort_order'))
    ->addIndex($installer->getIdxName('api_role', array('tree_level')),
        array('tree_level'))
    ->setComment('Api ACL Roles');
$installer->getConnection()->createTable($table);

/**
 * Create table 'api_rule'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('api_rule'))
    ->addColumn('rule_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Api rule Id')
    ->addColumn('role_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Api role Id')
    ->addColumn('resource_id', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Module code')
    ->addColumn('api_privileges', \Magento\DB\Ddl\Table::TYPE_TEXT, 20, array(
        ), 'Privileges')
    ->addColumn('assert_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Assert id')
    ->addColumn('role_type', \Magento\DB\Ddl\Table::TYPE_TEXT, 1, array(
        ), 'Role type')
    ->addColumn('api_permission', \Magento\DB\Ddl\Table::TYPE_TEXT, 10, array(
        ), 'Permission')
    ->addIndex($installer->getIdxName('api_rule', array('resource_id', 'role_id')),
        array('resource_id', 'role_id'))
    ->addIndex($installer->getIdxName('api_rule', array('role_id', 'resource_id')),
        array('role_id', 'resource_id'))
    ->addForeignKey($installer->getFkName('api_rule', 'role_id', 'api_role', 'role_id'),
        'role_id', $installer->getTable('api_role'), 'role_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->setComment('Api ACL Rules');
$installer->getConnection()->createTable($table);

/**
 * Create table 'api_user'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('api_user'))
    ->addColumn('user_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'User id')
    ->addColumn('firstname', \Magento\DB\Ddl\Table::TYPE_TEXT, 32, array(
        ), 'First name')
    ->addColumn('lastname', \Magento\DB\Ddl\Table::TYPE_TEXT, 32, array(
        ), 'Last name')
    ->addColumn('email', \Magento\DB\Ddl\Table::TYPE_TEXT, 128, array(
        ), 'Email')
    ->addColumn('username', \Magento\DB\Ddl\Table::TYPE_TEXT, 40, array(
        ), 'Nickname')
    ->addColumn('api_key', \Magento\DB\Ddl\Table::TYPE_TEXT, 40, array(
        ), 'Api key')
    ->addColumn('created', \Magento\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'User record create date')
    ->addColumn('modified', \Magento\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(
        ), 'User record modify date')
    ->addColumn('lognum', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Quantity of log ins')
    ->addColumn('reload_acl_flag', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Refresh ACL flag')
    ->addColumn('is_active', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '1',
        ), 'Account status')
    ->setComment('Api Users');
$installer->getConnection()->createTable($table);

/**
 * Create table 'api_session'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('api_session'))
    ->addColumn('user_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'User id')
    ->addColumn('logdate', \Magento\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Login date')
    ->addColumn('sessid', \Magento\DB\Ddl\Table::TYPE_TEXT, 40, array(
        ), 'Sessioin id')
    ->addIndex($installer->getIdxName('api_session', array('user_id')),
        array('user_id'))
    ->addIndex($installer->getIdxName('api_session', array('sessid')),
        array('sessid'))
    ->addForeignKey($installer->getFkName('api_session', 'user_id', 'api_user', 'user_id'),
        'user_id', $installer->getTable('api_user'), 'user_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->setComment('Api Sessions');
$installer->getConnection()->createTable($table);



$installer->endSetup();
