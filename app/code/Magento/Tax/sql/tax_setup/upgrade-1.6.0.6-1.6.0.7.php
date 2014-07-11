<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var \Magento\Tax\Model\Resource\Setup $installer */
$installer = $this;

/**
 * Add new field to 'sales_order_tax_item'
 */
$installer->getConnection()->addColumn(
    $installer->getTable('sales_order_tax_item'),
    'amount',
    [
        'TYPE' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
        'SCALE' => 4,
        'PRECISION' => 12,
        'NULLABLE' => false,
        'COMMENT' => 'Tax amount for the item and tax rate.'
    ]
)->addColumn(
    $installer->getTable('sales_order_tax_item'),
    'base_amount',
    [
        'TYPE' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
        'SCALE' => 4,
        'PRECISION' => 12,
        'NULLABLE' => false,
        'COMMENT' => 'Base tax amount for the item and tax rate.'
    ]
)->addColumn(
    $installer->getTable('sales_order_tax_item'),
    'real_amount',
    [
        'TYPE' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
        'SCALE' => 4,
        'PRECISION' => 12,
        'NULLABLE' => false,
        'COMMENT' => 'Real tax amount for the item and tax rate.'
    ]
)->addColumn(
    $installer->getTable('sales_order_tax_item'),
    'real_base_amount',
    [
        'TYPE' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
        'SCALE' => 4,
        'PRECISION' => 12,
        'NULLABLE' => false,
        'COMMENT' => 'Real base tax amount for the item and tax rate.'
    ]
)->addColumn(
    $installer->getTable('sales_order_tax_item'),
    'associated_item_id',
    [
        'TYPE' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        'UNSIGNED' => true,
        'NULLABLE' => true,
        'COMMENT' => 'Id of the associated item.'
    ]
)->addColumn(
    $installer->getTable('sales_order_tax_item'),
    'taxable_item_type',
    [
        'TYPE' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        'length' => 32,
        'NULLABLE' => false,
        'COMMENT' => 'Type of the taxable item.'
    ]
)->changeColumn(
    $installer->getTable('sales_order_tax_item'),
    'item_id',
    'item_id',
    [
        'TYPE' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        'NULLABLE' => true,
        'UNSIGNED' => true,
        'COMMENT' => 'Item Id',
    ]
)->addForeignKey(
    $installer->getFkName('sales_order_tax_item', 'associated_item_id', 'sales_flat_order_item', 'item_id'),
    $installer->getTable('sales_order_tax_item'),
    'associated_item_id',
    $installer->getTable('sales_flat_order_item'),
    'item_id'
);

