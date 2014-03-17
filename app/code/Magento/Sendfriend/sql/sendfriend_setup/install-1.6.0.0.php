<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sendfriend
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/* @var $installer \Magento\Core\Model\Resource\Setup */

$installer->startSetup();

$table = $installer->getConnection()->newTable(
    $installer->getTable('sendfriend_log')
)->addColumn(
    'log_id',
    \Magento\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Log ID'
)->addColumn(
    'ip',
    \Magento\DB\Ddl\Table::TYPE_BIGINT,
    '20',
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Customer IP address'
)->addColumn(
    'time',
    \Magento\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Log time'
)->addColumn(
    'website_id',
    \Magento\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Website ID'
)->addIndex(
    $installer->getIdxName('sendfriend_log', 'ip'),
    'ip'
)->addIndex(
    $installer->getIdxName('sendfriend_log', 'time'),
    'time'
)->setComment(
    'Send to friend function log storage table'
);
$installer->getConnection()->createTable($table);

$installer->endSetup();
