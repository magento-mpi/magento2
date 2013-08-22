<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/** @var $installer Magento_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('catalog_eav_attribute'), 'search_weight', array(
        'type'      => Magento_DB_Ddl_Table::TYPE_SMALLINT,
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        'comment'   => 'Search Weight',
    ));
$installer->getConnection()->addIndex($installer->getTable('catalogsearch_query'),
    $installer->getIdxName('catalogsearch_query', array('num_results')),
    'num_results');
$installer->getConnection()->addIndex($installer->getTable('catalogsearch_query'),
    $installer->getIdxName('catalogsearch_query', array('query_text')),
    'query_text');
$installer->getConnection()->addIndex($installer->getTable('catalogsearch_query'),
    $installer->getIdxName('catalogsearch_query', array('query_text', 'store_id', 'num_results')),
    array('query_text', 'store_id', 'num_results'));

$table = $installer->getConnection()
    ->newTable($installer->getTable('catalogsearch_recommendations'))
    ->addColumn('id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Id')
    ->addColumn('query_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Query Id')
    ->addColumn('relation_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Relation Id')
    ->addForeignKey($installer->getFkName('catalogsearch_recommendations', 'query_id', 'catalogsearch_query', 'query_id'),
        'query_id', $installer->getTable('catalogsearch_query'), 'query_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('catalogsearch_recommendations', 'relation_id', 'catalogsearch_query', 'query_id'),
        'relation_id', $installer->getTable('catalogsearch_query'), 'query_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->setComment('Enterprise Search Recommendations');
$installer->getConnection()->createTable($table);

$installer->endSetup();
