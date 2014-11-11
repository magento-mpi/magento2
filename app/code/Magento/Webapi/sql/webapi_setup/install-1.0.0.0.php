<?php
/**
 * Setup script for Webapi module.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Setup\Module\SetupModule */
$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()->newTable(
    $installer->getTable('webapi_role')
)->addColumn(
    'role_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Webapi role ID'
)->addColumn(
    'role_name',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array('nullable' => false),
    'Role name is displayed in Adminhtml interface'
)->addIndex(
    $installer->getIdxName(
        'webapi_role',
        array('role_name'),
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('role_name'),
    array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
)->setComment(
    'Roles of unified webapi ACL'
);
$installer->getConnection()->createTable($table);

$table = $installer->getConnection()->newTable(
    $installer->getTable('webapi_user')
)->addColumn(
    'user_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Webapi user ID'
)->addColumn(
    'user_name',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array('nullable' => false),
    'User name is displayed in Adminhtml interface'
)->addColumn(
    'role_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'default' => null, 'nullable' => true),
    'User role from webapi_role'
)->addIndex(
    $installer->getIdxName(
        'webapi_user',
        array('role_id'),
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
    ),
    array('role_id'),
    array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX)
)->addIndex(
    $installer->getIdxName(
        'webapi_user',
        array('user_name'),
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('user_name'),
    array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
)->addForeignKey(
    $installer->getFkName('webapi_user', 'role_id', 'webapi_role', 'role_id'),
    'role_id',
    $installer->getTable('webapi_role'),
    'role_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Users of unified webapi'
);
$installer->getConnection()->createTable($table);

$table = $installer->getConnection()->newTable(
    $installer->getTable('webapi_rule')
)->addColumn(
    'rule_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Rule ID'
)->addColumn(
    'resource_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array('nullable' => false),
    'Resource name. Must match resource calls in xml.'
)->addColumn(
    'role_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false),
    'User role from webapi_role'
)->addIndex(
    $installer->getIdxName(
        'webapi_rule',
        array('role_id'),
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
    ),
    array('role_id'),
    array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX)
)->addForeignKey(
    $installer->getFkName('webapi_rule', 'role_id', 'webapi_role', 'role_id'),
    'role_id',
    $installer->getTable('webapi_role'),
    'role_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Permissions of roles to resources'
);
$installer->getConnection()->createTable($table);

$installer->endSetup();
