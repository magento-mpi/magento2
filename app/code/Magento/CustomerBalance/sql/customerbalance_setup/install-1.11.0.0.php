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
 * Create table 'magento_customerbalance'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_customerbalance')
)->addColumn(
    'balance_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Balance Id'
)->addColumn(
    'customer_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Customer Id'
)->addColumn(
    'website_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true),
    'Website Id'
)->addColumn(
    'amount',
    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
    '12,4',
    array('nullable' => false, 'default' => '0.0000'),
    'Balance Amount'
)->addColumn(
    'base_currency_code',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    3,
    array(),
    'Base Currency Code'
)->addIndex(
    $installer->getIdxName(
        'magento_customerbalance',
        array('customer_id', 'website_id'),
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('customer_id', 'website_id'),
    array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
)->addIndex(
    $installer->getIdxName('magento_customerbalance', array('website_id')),
    array('website_id')
)->addForeignKey(
    $installer->getFkName('magento_customerbalance', 'website_id', 'store_website', 'website_id'),
    'website_id',
    $installer->getTable('store_website'),
    'website_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_customerbalance', 'customer_id', 'customer_entity', 'entity_id'),
    'customer_id',
    $installer->getTable('customer_entity'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Enterprise Customerbalance'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_customerbalance_history'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_customerbalance_history')
)->addColumn(
    'history_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'History Id'
)->addColumn(
    'balance_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Balance Id'
)->addColumn(
    'updated_at',
    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
    null,
    array(),
    'Updated At'
)->addColumn(
    'action',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Action'
)->addColumn(
    'balance_amount',
    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
    '12,4',
    array('nullable' => false, 'default' => '0.0000'),
    'Balance Amount'
)->addColumn(
    'balance_delta',
    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
    '12,4',
    array('nullable' => false, 'default' => '0.0000'),
    'Balance Delta'
)->addColumn(
    'additional_info',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Additional Info'
)->addColumn(
    'is_customer_notified',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Is Customer Notified'
)->addIndex(
    $installer->getIdxName('magento_customerbalance_history', array('balance_id')),
    array('balance_id')
)->addForeignKey(
    $installer->getFkName('magento_customerbalance_history', 'balance_id', 'magento_customerbalance', 'balance_id'),
    'balance_id',
    $installer->getTable('magento_customerbalance'),
    'balance_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Enterprise Customerbalance History'
);
$installer->getConnection()->createTable($table);

$installer->endSetup();
