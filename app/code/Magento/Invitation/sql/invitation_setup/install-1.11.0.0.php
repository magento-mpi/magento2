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
 * Create table 'magento_invitation'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_invitation')
)->addColumn(
    'invitation_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Invitation Id'
)->addColumn(
    'customer_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true),
    'Customer Id'
)->addColumn(
    'invitation_date',
    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
    null,
    array(),
    'Invitation Date'
)->addColumn(
    'email',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Email'
)->addColumn(
    'referral_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true),
    'Referral Id'
)->addColumn(
    'protection_code',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    32,
    array(),
    'Protection Code'
)->addColumn(
    'signup_date',
    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
    null,
    array(),
    'Signup Date'
)->addColumn(
    'store_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false),
    'Store Id'
)->addColumn(
    'group_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true),
    'Group Id'
)->addColumn(
    'message',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '64k',
    array(),
    'Message'
)->addColumn(
    'status',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    8,
    array('nullable' => false, 'default' => 'new'),
    'Status'
)->addIndex(
    $installer->getIdxName('magento_invitation', array('customer_id')),
    array('customer_id')
)->addIndex(
    $installer->getIdxName('magento_invitation', array('referral_id')),
    array('referral_id')
)->addIndex(
    $installer->getIdxName('magento_invitation', array('store_id')),
    array('store_id')
)->addIndex(
    $installer->getIdxName('magento_invitation', array('group_id')),
    array('group_id')
)->addForeignKey(
    $installer->getFkName('magento_invitation', 'group_id', 'customer_group', 'customer_group_id'),
    'group_id',
    $installer->getTable('customer_group'),
    'customer_group_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_invitation', 'customer_id', 'customer_entity', 'entity_id'),
    'customer_id',
    $installer->getTable('customer_entity'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_invitation', 'referral_id', 'customer_entity', 'entity_id'),
    'referral_id',
    $installer->getTable('customer_entity'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_invitation', 'store_id', 'store', 'store_id'),
    'store_id',
    $installer->getTable('store'),
    'store_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Enterprise Invitation'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_invitation_status_history'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_invitation_status_history')
)->addColumn(
    'history_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'History Id'
)->addColumn(
    'invitation_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false),
    'Invitation Id'
)->addColumn(
    'invitation_date',
    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
    null,
    array(),
    'Invitation Date'
)->addColumn(
    'status',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    8,
    array('nullable' => false, 'default' => 'new'),
    'Invitation Status'
)->addIndex(
    $installer->getIdxName('magento_invitation_status_history', array('invitation_id')),
    array('invitation_id')
)->addForeignKey(
    $installer->getFkName('magento_invitation_status_history', 'invitation_id', 'magento_invitation', 'invitation_id'),
    'invitation_id',
    $installer->getTable('magento_invitation'),
    'invitation_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Enterprise Invitation Status History'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_invitation_track'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_invitation_track')
)->addColumn(
    'track_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Track Id'
)->addColumn(
    'inviter_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Inviter Id'
)->addColumn(
    'referral_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Referral Id'
)->addIndex(
    $installer->getIdxName(
        'magento_invitation_track',
        array('inviter_id', 'referral_id'),
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('inviter_id', 'referral_id'),
    array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
)->addIndex(
    $installer->getIdxName('magento_invitation_track', array('referral_id')),
    array('referral_id')
)->addForeignKey(
    $installer->getFkName('magento_invitation_track', 'inviter_id', 'customer_entity', 'entity_id'),
    'inviter_id',
    $installer->getTable('customer_entity'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_invitation_track', 'referral_id', 'customer_entity', 'entity_id'),
    'referral_id',
    $installer->getTable('customer_entity'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Enterprise Invitation Track'
);
$installer->getConnection()->createTable($table);

$installer->endSetup();
