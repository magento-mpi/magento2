<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Setup\Module\SetupModule */
$installer = $this;

$tableName = $installer->getTable('sales_flat_order_item');

$installer->startSetup();

$installer->getConnection()->addColumn(
    $tableName,
    'qty_returned',
    array(
        'TYPE' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
        'SCALE' => 4,
        'PRECISION' => 12,
        'DEFAULT' => '0.0000',
        'NULLABLE' => false,
        'COMMENT' => 'Qty of returned items'
    )
);
$installer->endSetup();
