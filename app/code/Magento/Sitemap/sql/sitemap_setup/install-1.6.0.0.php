<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sitemap
 * @copyright   {copyright}
 * @license     {license_link}
 */


/* @var $installer Magento_Core_Model_Resource_Setup */

$installer = $this;

$installer->startSetup();

/**
 * Create table 'sitemap'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sitemap'))
    ->addColumn('sitemap_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Sitemap Id')
    ->addColumn('sitemap_type', Magento_DB_Ddl_Table::TYPE_TEXT, 32, array(
        ), 'Sitemap Type')
    ->addColumn('sitemap_filename', Magento_DB_Ddl_Table::TYPE_TEXT, 32, array(
        ), 'Sitemap Filename')
    ->addColumn('sitemap_path', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Sitemap Path')
    ->addColumn('sitemap_time', Magento_DB_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => true,
        ), 'Sitemap Time')
    ->addColumn('store_id', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store id')
    ->addIndex($installer->getIdxName('sitemap', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('sitemap', 'store_id', 'core_store', 'store_id'),
        'store_id', $installer->getTable('core_store'), 'store_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE,
        Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->setComment('XML Sitemap');

$installer->getConnection()->createTable($table);

$installer->endSetup();
