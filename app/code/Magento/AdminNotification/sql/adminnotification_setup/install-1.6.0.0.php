<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Notification id'
)->addColumn(
    'severity',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Problem type'
)->addColumn(
    'date_added',
    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
    null,
    array('nullable' => false),
    'Create date'
)->addColumn(
    'title',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array('nullable' => false),
    'Title'
)->addColumn(
    'description',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '64k',
    array(),
    'Description'
)->addColumn(
    'url',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Url'
)->addColumn(
    'is_read',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Flag if notification read'
)->addColumn(
    'is_remove',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Flag if notification might be removed'
)->addIndex(
    $installer->getIdxName('adminnotification_inbox', array('severity')),
    array('severity')
)->addIndex(
    $installer->getIdxName('adminnotification_inbox', array('is_read')),
    array('is_read')
)->addIndex(
    $installer->getIdxName('adminnotification_inbox', array('is_remove')),
    array('is_remove')
)->setComment(
    'Adminnotification Inbox'
);
$installer->getConnection()->createTable($table);

$installer->endSetup();
