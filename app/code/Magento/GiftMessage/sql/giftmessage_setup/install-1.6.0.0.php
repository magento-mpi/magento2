<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/** @var $installer Magento\Setup\Module\SetupModule */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'gift_message'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('gift_message')
)->addColumn(
    'gift_message_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'GiftMessage Id'
)->addColumn(
    'customer_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Customer id'
)->addColumn(
    'sender',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Sender'
)->addColumn(
    'recipient',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Recipient'
)->addColumn(
    'message',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    null,
    array(),
    'Message'
)->setComment(
    'Gift Message'
);

$installer->getConnection()->createTable($table);

$installer->endSetup();
