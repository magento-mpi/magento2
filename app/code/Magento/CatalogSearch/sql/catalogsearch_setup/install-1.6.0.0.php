<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/* @var $installer Magento_Core_Model_Resource_Setup */

$installer->startSetup();

/**
 * Create table 'catalogsearch_query'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalogsearch_query'))
    ->addColumn('query_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Query ID')
    ->addColumn('query_text', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Query text')
    ->addColumn('num_results', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Num results')
    ->addColumn('popularity', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Popularity')
    ->addColumn('redirect', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Redirect')
    ->addColumn('synonym_for', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Synonym for')
    ->addColumn('store_id', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store ID')
    ->addColumn('display_in_terms', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '1',
        ), 'Display in terms')
    ->addColumn('is_active', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'default'   => '1',
        ), 'Active status')
    ->addColumn('is_processed', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'default'   => '0',
        ), 'Processed status')
    ->addColumn('updated_at', Magento_DB_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Updated at')
    ->addIndex($installer->getIdxName('catalogsearch_query', array('query_text','store_id','popularity')),
        array('query_text','store_id','popularity'))
    ->addIndex($installer->getIdxName('catalogsearch_query', 'store_id'), 'store_id')
    ->addForeignKey($installer->getFkName('catalogsearch_query', 'store_id', 'core_store', 'store_id'),
        'store_id', $installer->getTable('core_store'), 'store_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog search query table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalogsearch_result'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalogsearch_result'))
    ->addColumn('query_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Query ID')
    ->addColumn('product_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Product ID')
    ->addColumn('relevance', Magento_DB_Ddl_Table::TYPE_DECIMAL, '20,4', array(
        'nullable'  => false,
        'default'   => '0.0000'
        ), 'Relevance')
    ->addIndex($installer->getIdxName('catalogsearch_result', 'query_id'), 'query_id')
    ->addForeignKey($installer->getFkName('catalogsearch_result', 'query_id', 'catalogsearch_query', 'query_id'),
        'query_id', $installer->getTable('catalogsearch_query'), 'query_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->addIndex($installer->getIdxName('catalogsearch_result', 'product_id'), 'product_id')
    ->addForeignKey($installer->getFkName('catalogsearch_result', 'product_id', 'catalog_product_entity', 'entity_id'),
        'product_id', $installer->getTable('catalog_product_entity'), 'entity_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog search result table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalogsearch_fulltext'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalogsearch_fulltext'))
    ->addColumn('fulltext_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID')
    ->addColumn('product_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Product ID')
    ->addColumn('store_id', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Store ID')
    ->addColumn('data_index', Magento_DB_Ddl_Table::TYPE_TEXT, '4g', array(
        ), 'Data index')
    ->addIndex(
        $installer->getIdxName(
            'catalogsearch_fulltext',
            array('product_id', 'store_id'),
            Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('product_id', 'store_id'),
        array('type' => Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex(
        $installer->getIdxName(
            'catalogsearch_fulltext',
            'data_index',
            Magento_DB_Adapter_Interface::INDEX_TYPE_FULLTEXT
         ),
        'data_index',
        array('type' => Magento_DB_Adapter_Interface::INDEX_TYPE_FULLTEXT))
    ->setOption('type', 'MyISAM')
    ->setComment('Catalog search result table');
$installer->getConnection()->createTable($table);

$installer->endSetup();
