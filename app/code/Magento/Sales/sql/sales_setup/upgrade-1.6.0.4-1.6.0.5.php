<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Setup\Module\SetupModule */
$installer = $this;
$installer->getConnection()->addColumn(
    $installer->getTable('sales_flat_order_item'),
    'base_tax_refunded',
    array(
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
        'comment' => 'Base Tax Refunded',
        'scale' => 4,
        'precision' => 12
    )
);
$installer->getConnection()->addColumn(
    $installer->getTable('sales_flat_order_item'),
    'discount_refunded',
    array(
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
        'comment' => 'Discount Refunded',
        'scale' => 4,
        'precision' => 12
    )
);
$installer->getConnection()->addColumn(
    $installer->getTable('sales_flat_order_item'),
    'base_discount_refunded',
    array(
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
        'comment' => 'Base Discount Refunded',
        'scale' => 4,
        'precision' => 12
    )
);
