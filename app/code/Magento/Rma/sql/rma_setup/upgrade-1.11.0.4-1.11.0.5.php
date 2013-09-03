<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Rma_Model_Resource_Setup */
$installer = $this;

/**
 * Create table 'magento_rma_shipping_label'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_rma_shipping_label'))
    ->addColumn('entity_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity Id')
    ->addColumn('rma_entity_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'RMA Entity Id')
    ->addColumn('shipping_label', Magento_DB_Ddl_Table::TYPE_VARBINARY, '2M', array(
        ), 'Shipping Label Content')
    ->addColumn('packages', Magento_DB_Ddl_Table::TYPE_TEXT, 20000, array(
        ), 'Packed Products in Packages')
    ->addColumn('track_number', Magento_DB_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Tracking Number')
    ->addColumn('carrier_title', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Carrier Title')
    ->addColumn('method_title', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Method Title')
    ->addColumn('carrier_code', Magento_DB_Ddl_Table::TYPE_TEXT, 32, array(
        ), 'Carrier Code')
    ->addColumn('method_code', Magento_DB_Ddl_Table::TYPE_TEXT, 32, array(
        ), 'Method Code')
    ->addColumn('price', Magento_DB_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Price')
    ->addForeignKey(
        $installer->getFkName('magento_rma_shipping_label', 'rma_entity_id', 'magento_rma', 'entity_id'),
        'rma_entity_id',
        $installer->getTable('magento_rma'),
        'entity_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE,
        Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->setComment('List of RMA Shipping Labels');
$installer->getConnection()->createTable($table);
