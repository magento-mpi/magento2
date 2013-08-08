<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Magento_Core_Model_Resource_Setup */
$installer = $this;

/**
 * Create table 'magento_customercustomattributes_sales_flat_order'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_customercustomattributes_sales_flat_order'))
    ->addColumn('entity_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Entity Id')
    ->addForeignKey($installer->getFkName('magento_customercustomattributes_sales_flat_order', 'entity_id', 'sales_flat_order', 'entity_id'),
        'entity_id', $installer->getTable('sales_flat_order'), 'entity_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->setComment('Enterprise Customer Sales Flat Order');
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_customercustomattributes_sales_flat_order_address'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_customercustomattributes_sales_flat_order_address'))
    ->addColumn('entity_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Entity Id')
    ->addForeignKey($installer->getFkName('magento_customercustomattributes_sales_flat_order_address', 'entity_id', 'sales_flat_order_address', 'entity_id'),
        'entity_id', $installer->getTable('sales_flat_order_address'), 'entity_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->setComment('Enterprise Customer Sales Flat Order Address');
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_customercustomattributes_sales_flat_quote'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_customercustomattributes_sales_flat_quote'))
    ->addColumn('entity_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Entity Id')
    ->addForeignKey($installer->getFkName('magento_customercustomattributes_sales_flat_quote', 'entity_id', 'sales_flat_quote', 'entity_id'),
        'entity_id', $installer->getTable('sales_flat_quote'), 'entity_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->setComment('Enterprise Customer Sales Flat Quote');
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_customercustomattributes_sales_flat_quote_address'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_customercustomattributes_sales_flat_quote_address'))
    ->addColumn('entity_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Entity Id')
    ->addForeignKey($installer->getFkName('magento_customercustomattributes_sales_flat_quote_address', 'entity_id', 'sales_flat_quote_address', 'address_id'),
        'entity_id', $installer->getTable('sales_flat_quote_address'), 'address_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->setComment('Enterprise Customer Sales Flat Quote Address');
$installer->getConnection()->createTable($table);
