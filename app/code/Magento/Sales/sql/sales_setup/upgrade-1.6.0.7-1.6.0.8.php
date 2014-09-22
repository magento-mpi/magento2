<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Sales\Model\Resource\Setup */
$installer = $this;

$invoiceTable = $installer->getTable('sales_flat_invoice');
$installer->getConnection()->addColumn(
    $invoiceTable,
    'discount_description',
    array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'length' => 255, 'comment' => 'Discount Description')
);

$creditmemoTable = $installer->getTable('sales_flat_creditmemo');
$installer->getConnection()->addColumn(
    $creditmemoTable,
    'discount_description',
    array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'length' => 255, 'comment' => 'Discount Description')
);
