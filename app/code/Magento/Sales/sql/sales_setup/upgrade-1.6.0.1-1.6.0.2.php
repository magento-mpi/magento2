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
$installer->getConnection()->addColumn(
    $installer->getTable('sales_flat_shipment'),
    'packages',
    array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'comment' => 'Packed Products in Packages', 'length' => '20000')
);
$installer->endSetup();
