<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Sales\Model\Resource\Setup */
$installer = $this;
$installer->getConnection()
    ->addColumn($installer->getTable('sales_flat_order_item'), 'base_tax_refunded', array(
        'type'    => \Magento\DB\Ddl\Table::TYPE_DECIMAL,
        'comment' => 'Base Tax Refunded',
        'scale'     => 4,
        'precision' => 12,
    ));
$installer->getConnection()
    ->addColumn($installer->getTable('sales_flat_order_item'), 'discount_refunded', array(
        'type'    => \Magento\DB\Ddl\Table::TYPE_DECIMAL,
        'comment' => 'Discount Refunded',
        'scale'     => 4,
        'precision' => 12,
    ));
$installer->getConnection()
    ->addColumn($installer->getTable('sales_flat_order_item'), 'base_discount_refunded', array(
        'type'    => \Magento\DB\Ddl\Table::TYPE_DECIMAL,
        'comment' => 'Base Discount Refunded',
        'scale'     => 4,
        'precision' => 12,
    ));
