<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Setup\Module\SetupModule */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'checkout_agreement'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('checkout_agreement')
)->addColumn(
    'agreement_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Agreement Id'
)->addColumn(
    'name',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Name'
)->addColumn(
    'content',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '64k',
    array(),
    'Content'
)->addColumn(
    'content_height',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    25,
    array(),
    'Content Height'
)->addColumn(
    'checkbox_text',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '64k',
    array(),
    'Checkbox Text'
)->addColumn(
    'is_active',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('nullable' => false, 'default' => '0'),
    'Is Active'
)->addColumn(
    'is_html',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('nullable' => false, 'default' => '0'),
    'Is Html'
)->setComment(
    'Checkout Agreement'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'checkout_agreement_store'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('checkout_agreement_store')
)->addColumn(
    'agreement_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Agreement Id'
)->addColumn(
    'store_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Store Id'
)->addForeignKey(
    $installer->getFkName('checkout_agreement_store', 'agreement_id', 'checkout_agreement', 'agreement_id'),
    'agreement_id',
    $installer->getTable('checkout_agreement'),
    'agreement_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('checkout_agreement_store', 'store_id', 'store', 'store_id'),
    'store_id',
    $installer->getTable('store'),
    'store_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Checkout Agreement Store'
);
$installer->getConnection()->createTable($table);

$installer->endSetup();
