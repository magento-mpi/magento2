<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Mage_Sales_Model_Resource_Setup */
$installer = $this;

$invoiceTable = $installer->getTable('sales_flat_invoice');
$installer->getConnection()
    ->addColumn($invoiceTable, 'discount_description', array(
            'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length'    => 255,
            'comment'   => 'Discount Description'
    ));

$creditmemoTable = $installer->getTable('sales_flat_creditmemo');
$installer->getConnection()
    ->addColumn($creditmemoTable, 'discount_description', array(
            'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length'    => 255,
            'comment'   => 'Discount Description'
    ));
