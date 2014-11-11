<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Setup\Module\SetupModule */
$installer = $this;

$installer->startSetup();
/**
 * Create table 'admin_system_messages'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('admin_system_messages')
)->addColumn(
    'identity',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    100,
    array('nullable' => false, 'primary' => true),
    'Message id'
)->addColumn(
    'severity',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Problem type'
)->addColumn(
    'created_at',
    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
    null,
    array('nullable' => false),
    'Create date'
)->setComment(
    'Admin System Messages'
);
$installer->getConnection()->createTable($table);

$installer->endSetup();
