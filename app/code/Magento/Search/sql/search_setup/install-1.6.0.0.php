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
 * Create table 'search_query'
 */
$table = $installer->getConnection()
->newTable($installer->getTable('search_query'))
->addColumn(
    'query_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Query ID'
)->addColumn(
    'query_text',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Query text'
)->addColumn(
    'num_results',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Num results'
)->addColumn(
    'popularity',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Popularity'
)->addColumn(
    'redirect',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Redirect'
)->addColumn(
    'synonym_for',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Synonym for'
)->addColumn(
    'store_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Store ID'
)->addColumn(
    'display_in_terms',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('nullable' => false, 'default' => '1'),
    'Display in terms'
)->addColumn(
    'is_active',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('default' => '1'),
    'Active status'
)->addColumn(
    'is_processed',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('default' => '0'),
    'Processed status'
)->addColumn(
    'updated_at',
    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
    null,
    array('nullable' => false),
    'Updated at'
)->addIndex(
    $installer->getIdxName('search_query', array('query_text', 'store_id', 'popularity')),
    array('query_text', 'store_id', 'popularity')
)->addIndex(
    $installer->getIdxName('search_query', 'store_id'),
    'store_id'
)->addForeignKey(
    $installer->getFkName('search_query', 'store_id', 'store', 'store_id'),
    'store_id',
    $installer->getTable('store'),
    'store_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Catalog search query table'
);
$installer->getConnection()->createTable($table);

$installer->endSetup();
