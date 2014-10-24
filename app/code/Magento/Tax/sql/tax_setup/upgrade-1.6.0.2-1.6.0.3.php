<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Magento\Setup\Module\SetupModule */
$installer = $this;

/**
 * Add new field to 'sales_order_tax_item'
 */
$installer->getConnection()->addColumn(
    $installer->getTable('sales_order_tax_item'),
    'tax_percent',
    array(
        'TYPE' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
        'SCALE' => 4,
        'PRECISION' => 12,
        'NULLABLE' => false,
        'COMMENT' => 'Real Tax Percent For Item'
    )
);
