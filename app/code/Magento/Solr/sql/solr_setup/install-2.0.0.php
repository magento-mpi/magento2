<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Setup\Module\SetupModule */
$installer = $this;

$installer->startSetup();

$installer->getConnection()->addColumn(
    $installer->getTable('catalog_eav_attribute'),
    'search_weight',
    [
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        'unsigned' => true,
        'nullable' => false,
        'default' => '1',
        'comment' => 'Search Weight'
    ]
);
$installer->getConnection()->addIndex(
    $installer->getTable('search_query'),
    $installer->getIdxName('search_query', ['num_results']),
    'num_results'
);
$installer->getConnection()->addIndex(
    $installer->getTable('search_query'),
    $installer->getIdxName('search_query', ['query_text', 'store_id', 'num_results']),
    ['query_text', 'store_id', 'num_results']
);

$table = $installer->getConnection()->newTable(
    $installer->getTable('catalogsearch_recommendations')
)->addColumn(
    'id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
    'Id'
)->addColumn(
    'query_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
    'Query Id'
)->addColumn(
    'relation_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
    'Relation Id'
)->addForeignKey(
    $installer->getFkName('catalogsearch_recommendations', 'query_id', 'search_query', 'query_id'),
    'query_id',
    $installer->getTable('search_query'),
    'query_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('catalogsearch_recommendations', 'relation_id', 'search_query', 'query_id'),
    'relation_id',
    $installer->getTable('search_query'),
    'query_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment('Enterprise Search Recommendations');
$installer->getConnection()->createTable($table);

$installer->endSetup();
