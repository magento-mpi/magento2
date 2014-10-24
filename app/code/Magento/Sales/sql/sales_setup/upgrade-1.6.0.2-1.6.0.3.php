<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Magento\Setup\Module\SetupModule */
$installer = $this;
$installer->getConnection()->addColumn(
    $installer->getTable('sales_flat_shipment'),
    'shipping_label',
    array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_VARBINARY, 'comment' => 'Shipping Label Content', 'length' => '2m')
);
