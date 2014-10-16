<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/* @var $installer \Magento\Framework\Module\Setup */

$installer->startSetup();

/**
 * Create table 'catalogsearch_result'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('catalogsearch_result')
)->addColumn(
    'query_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Query ID'
)->addColumn(
    'product_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Product ID'
)->addColumn(
    'relevance',
    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
    '20,4',
    array('nullable' => false, 'default' => '0.0000'),
    'Relevance'
)->addForeignKey(
    $installer->getFkName('catalogsearch_result', 'query_id', 'search_query', 'query_id'),
    'query_id',
    $installer->getTable('search_query'),
    'query_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addIndex(
    $installer->getIdxName('catalogsearch_result', 'product_id'),
    'product_id'
)->addForeignKey(
    $installer->getFkName('catalogsearch_result', 'product_id', 'catalog_product_entity', 'entity_id'),
    'product_id',
    $installer->getTable('catalog_product_entity'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Catalog search result table'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalogsearch_fulltext'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('catalogsearch_fulltext')
)->addColumn(
    'fulltext_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Entity ID'
)->addColumn(
    'product_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false),
    'Product ID'
)->addColumn(
    'store_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false),
    'Store ID'
)->addColumn(
    'data_index',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '4g',
    array(),
    'Data index'
)->addIndex(
    $installer->getIdxName(
        'catalogsearch_fulltext',
        array('product_id', 'store_id'),
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('product_id', 'store_id'),
    array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
)->addIndex(
    $installer->getIdxName(
        'catalogsearch_fulltext',
        'data_index',
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
    ),
    'data_index',
    array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT)
)->setOption(
    'type',
    'MyISAM'
)->setComment(
    'Catalog search result table'
);
$installer->getConnection()->createTable($table);

$installer->endSetup();
