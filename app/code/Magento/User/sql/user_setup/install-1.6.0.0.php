<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Magento\Setup\Module\SetupModule */
$installer = $this;

$installer->startSetup();

/**
 * Create table 'admin_assert'
 */
if (!$installer->getConnection()->isTableExists($installer->getTable('admin_assert'))) {
    $table = $installer->getConnection()->newTable(
        $installer->getTable('admin_assert')
    )->addColumn(
        'assert_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Assert ID'
    )->addColumn(
        'assert_type',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        20,
        array('nullable' => true, 'default' => null),
        'Assert Type'
    )->addColumn(
        'assert_data',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        '64k',
        array(),
        'Assert Data'
    )->setComment(
        'Admin Assert Table'
    );
    $installer->getConnection()->createTable($table);
}

/**
 * Create table 'admin_user'
 */
if (!$installer->getConnection()->isTableExists($installer->getTable('admin_user'))) {
    $table = $installer->getConnection()->newTable(
        $installer->getTable('admin_user')
    )->addColumn(
        'user_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'User ID'
    )->addColumn(
        'firstname',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        32,
        array('nullable' => true),
        'User First Name'
    )->addColumn(
        'lastname',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        32,
        array('nullable' => true),
        'User Last Name'
    )->addColumn(
        'email',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        128,
        array('nullable' => true),
        'User Email'
    )->addColumn(
        'username',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        40,
        array('nullable' => true),
        'User Login'
    )->addColumn(
        'password',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        40,
        array('nullable' => true),
        'User Password'
    )->addColumn(
        'created',
        \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
        null,
        array('nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT),
        'User Created Time'
    )->addColumn(
        'modified',
        \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
        null,
        array(),
        'User Modified Time'
    )->addColumn(
        'logdate',
        \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
        null,
        array(),
        'User Last Login Time'
    )->addColumn(
        'lognum',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        array('unsigned' => true, 'nullable' => false, 'default' => '0'),
        'User Login Number'
    )->addColumn(
        'reload_acl_flag',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        array('nullable' => false, 'default' => '0'),
        'Reload ACL'
    )->addColumn(
        'is_active',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        array('nullable' => false, 'default' => '1'),
        'User Is Active'
    )->addColumn(
        'extra',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        '64k',
        array(),
        'User Extra Data'
    )->addIndex(
        $installer->getIdxName(
            'admin_user',
            array('username'),
            \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
        ),
        array('username'),
        array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
    )->setComment(
        'Admin User Table'
    );
    $installer->getConnection()->createTable($table);
}
$installer->endSetup();
