<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * AdminNotification install
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

/** @var $installer \Magento\Setup\Module\SetupModule */
$installer = $this;

$installer->startSetup();
/**
 * Create table 'adminnotification_inbox'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('adminnotification_inbox')
)->addColumn(
    'notification_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
    'Notification id'
)->addColumn(
    'severity',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
    'Problem type'
)->addColumn(
    'date_added',
    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
    null,
    ['nullable' => false],
    'Create date'
)->addColumn(
    'title',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    ['nullable' => false],
    'Title'
)->addColumn(
    'description',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '64k',
    [],
    'Description'
)->addColumn(
    'url',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    [],
    'Url'
)->addColumn(
    'is_read',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
    'Flag if notification read'
)->addColumn(
    'is_remove',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
    'Flag if notification might be removed'
)->addIndex(
    $installer->getIdxName('adminnotification_inbox', ['severity']),
    ['severity']
)->addIndex(
    $installer->getIdxName('adminnotification_inbox', ['is_read']),
    ['is_read']
)->addIndex(
    $installer->getIdxName('adminnotification_inbox', ['is_remove']),
    ['is_remove']
)->setComment(
    'Adminnotification Inbox'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'admin_system_messages'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('admin_system_messages')
)->addColumn(
    'identity',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    100,
    ['nullable' => false, 'primary' => true],
    'Message id'
)->addColumn(
    'severity',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
    'Problem type'
)->addColumn(
    'created_at',
    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
    null,
    ['nullable' => false],
    'Create date'
)->setComment(
    'Admin System Messages'
);
$installer->getConnection()->createTable($table);

$installer->endSetup();
