<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/* @var $installer \Magento\Framework\Module\Setup */
$installer->startSetup();

/**
 * Create table 'admin_role'
 */
if (!$installer->getConnection()->isTableExists($installer->getTable('admin_role'))) {
    $table = $installer->getConnection()->newTable(
        $installer->getTable('admin_role')
    )->addColumn(
        'role_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Role ID'
    )->addColumn(
        'parent_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        array('unsigned' => true, 'nullable' => false, 'default' => '0'),
        'Parent Role ID'
    )->addColumn(
        'tree_level',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        array('unsigned' => true, 'nullable' => false, 'default' => '0'),
        'Role Tree Level'
    )->addColumn(
        'sort_order',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        array('unsigned' => true, 'nullable' => false, 'default' => '0'),
        'Role Sort Order'
    )->addColumn(
        'role_type',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        1,
        array('nullable' => false, 'default' => '0'),
        'Role Type'
    )->addColumn(
        'user_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        array('unsigned' => true, 'nullable' => false, 'default' => '0'),
        'User ID'
    )->addColumn(
        'user_type',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        16,
        array('nullable' => true, 'default' => null),
        'User Type'
    )->addColumn(
        'role_name',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        50,
        array('nullable' => true, 'default' => null),
        'Role Name'
    )->addIndex(
        $installer->getIdxName('admin_role', array('parent_id', 'sort_order')),
        array('parent_id', 'sort_order')
    )->addIndex(
        $installer->getIdxName('admin_role', array('tree_level')),
        array('tree_level')
    )->setComment(
        'Admin Role Table'
    );
    $installer->getConnection()->createTable($table);
}
/**
 * Create table 'admin_rule'
 */
if (!$installer->getConnection()->isTableExists($installer->getTable('admin_rule'))) {
    $table = $installer->getConnection()->newTable(
        $installer->getTable('admin_rule')
    )->addColumn(
        'rule_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Rule ID'
    )->addColumn(
        'role_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        array('unsigned' => true, 'nullable' => false, 'default' => '0'),
        'Role ID'
    )->addColumn(
        'resource_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        255,
        array('nullable' => true, 'default' => null),
        'Resource ID'
    )->addColumn(
        'privileges',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        20,
        array('nullable' => true),
        'Privileges'
    )->addColumn(
        'permission',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        10,
        array(),
        'Permission'
    )->addIndex(
        $installer->getIdxName('admin_rule', array('resource_id', 'role_id')),
        array('resource_id', 'role_id')
    )->addIndex(
        $installer->getIdxName('admin_rule', array('role_id', 'resource_id')),
        array('role_id', 'resource_id')
    )->addForeignKey(
        $installer->getFkName('admin_rule', 'role_id', 'admin_role', 'role_id'),
        'role_id',
        $installer->getTable('admin_role'),
        'role_id',
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
    )->setComment(
        'Admin Rule Table'
    );
    $installer->getConnection()->createTable($table);
}

$installer->endSetup();
