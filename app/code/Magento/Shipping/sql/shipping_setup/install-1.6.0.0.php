<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/** @var $installer Magento_Core_Model_Resource_Setup */

$installer->startSetup();

/**
 * Create table 'shipping_tablerate'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('shipping_tablerate'))
    ->addColumn('pk', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Primary key')
    ->addColumn('website_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Website Id')
    ->addColumn('dest_country_id', Magento_DB_Ddl_Table::TYPE_TEXT, 4, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Destination coutry ISO/2 or ISO/3 code')
    ->addColumn('dest_region_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Destination Region Id')
    ->addColumn('dest_zip', Magento_DB_Ddl_Table::TYPE_TEXT, 10, array(
        'nullable'  => false,
        'default'   => '*',
        ), 'Destination Post Code (Zip)')
    ->addColumn('condition_name', Magento_DB_Ddl_Table::TYPE_TEXT, 20, array(
        'nullable'  => false,
        ), 'Rate Condition name')
    ->addColumn('condition_value', Magento_DB_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Rate condition value')
    ->addColumn('price', Magento_DB_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Price')
    ->addColumn('cost', Magento_DB_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Cost')
    ->addIndex($installer->getIdxName('shipping_tablerate', array('website_id', 'dest_country_id', 'dest_region_id', 'dest_zip', 'condition_name', 'condition_value'), Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('website_id', 'dest_country_id', 'dest_region_id', 'dest_zip', 'condition_name', 'condition_value'), array('type' => Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->setComment('Shipping Tablerate');
$installer->getConnection()->createTable($table);

$installer->endSetup();
