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
 * Create table 'magento_giftcardaccount'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_giftcardaccount')
)->addColumn(
    'giftcardaccount_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Giftcardaccount Id'
)->addColumn(
    'code',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array('nullable' => false),
    'Code'
)->addColumn(
    'status',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('nullable' => false),
    'Status'
)->addColumn(
    'date_created',
    \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
    null,
    array('nullable' => false),
    'Date Created'
)->addColumn(
    'date_expires',
    \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
    null,
    array(),
    'Date Expires'
)->addColumn(
    'website_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Website Id'
)->addColumn(
    'balance',
    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
    '12,4',
    array('nullable' => false, 'default' => '0.0000'),
    'Balance'
)->addColumn(
    'state',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('nullable' => false, 'default' => '0'),
    'State'
)->addColumn(
    'is_redeemable',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('nullable' => false, 'default' => '1'),
    'Is Redeemable'
)->addIndex(
    $installer->getIdxName('magento_giftcardaccount', array('website_id')),
    array('website_id')
)->addForeignKey(
    $installer->getFkName('magento_giftcardaccount', 'website_id', 'store_website', 'website_id'),
    'website_id',
    $installer->getTable('store_website'),
    'website_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Enterprise Giftcardaccount'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_giftcardaccount_pool'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_giftcardaccount_pool')
)->addColumn(
    'code',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array('nullable' => false, 'primary' => true),
    'Code'
)->addColumn(
    'status',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('nullable' => false, 'default' => '0'),
    'Status'
)->setComment(
    'Enterprise Giftcardaccount Pool'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_giftcardaccount_history'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_giftcardaccount_history')
)->addColumn(
    'history_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'History Id'
)->addColumn(
    'giftcardaccount_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Giftcardaccount Id'
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
)->addIndex(
    $installer->getIdxName('magento_giftcardaccount_history', array('giftcardaccount_id')),
    array('giftcardaccount_id')
)->addForeignKey(
    $installer->getFkName(
        'magento_giftcardaccount_history',
        'giftcardaccount_id',
        'magento_giftcardaccount',
        'giftcardaccount_id'
    ),
    'giftcardaccount_id',
    $installer->getTable('magento_giftcardaccount'),
    'giftcardaccount_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Enterprise Giftcardaccount History'
);
$installer->getConnection()->createTable($table);

$installer->endSetup();
