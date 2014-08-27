<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/* @var $installer \Magento\Customer\Model\Resource\Setup */

$installer->startSetup();

/**
 * Create table 'visitor'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('customer_visitor')
)->addColumn(
    'visitor_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Visitor ID'
)->addColumn(
    'session_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    64,
    array('nullable' => true, 'default' => null),
    'Session ID'
)->setComment(
    'Visitor Table'
);
$installer->getConnection()->createTable($table);

$installer->endSetup();
