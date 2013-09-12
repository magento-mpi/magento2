<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Magento_Sales_Model_Resource_Setup */
$installer = $this;

$installer->installEntities();

$invoiceTable = $installer->getTable('sales_flat_invoice');
$installer->getConnection()
    ->addColumn($invoiceTable, 'discount_description', array(
            'type'      => Magento_DB_Ddl_Table::TYPE_TEXT,
            'length'    => 255,
            'comment'   => 'Discount Description'
    ));

$creditmemoTable = $installer->getTable('sales_flat_creditmemo');
$installer->getConnection()
    ->addColumn($creditmemoTable, 'discount_description', array(
            'type'      => Magento_DB_Ddl_Table::TYPE_TEXT,
            'length'    => 255,
            'comment'   => 'Discount Description'
    ));
