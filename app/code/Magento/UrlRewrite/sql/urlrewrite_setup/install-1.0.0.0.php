<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Core\Model\Resource\Setup */
$installer = $this;

$installer->startSetup();

/**
 * Create table 'core_url_rewrite'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('core_url_rewrite')
)->addColumn(
    'url_rewrite_id',
    \Magento\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Rewrite Id'
)->addColumn(
    'store_id',
    \Magento\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Store Id'
)->addColumn(
    'id_path',
    \Magento\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Id Path'
)->addColumn(
    'request_path',
    \Magento\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Request Path'
)->addColumn(
    'target_path',
    \Magento\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Target Path'
)->addColumn(
    'is_system',
    \Magento\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'default' => '1'),
    'Defines is Rewrite System'
)->addColumn(
    'options',
    \Magento\DB\Ddl\Table::TYPE_TEXT,
    255,
    array('nullable' => true),
    'Options'
)->addColumn(
    'description',
    \Magento\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Deascription'
)->addIndex(
    $installer->getIdxName(
        'core_url_rewrite',
        array('request_path', 'store_id'),
        \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('request_path', 'store_id'),
    array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
)->addIndex(
    $installer->getIdxName(
        'core_url_rewrite',
        array('id_path', 'is_system', 'store_id'),
        \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('id_path', 'is_system', 'store_id'),
    array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
)->addIndex(
    $installer->getIdxName('core_url_rewrite', array('target_path', 'store_id')),
    array('target_path', 'store_id')
)->addIndex(
    $installer->getIdxName('core_url_rewrite', array('id_path')),
    array('id_path')
)->addIndex(
    $installer->getIdxName('core_url_rewrite', array('store_id')),
    array('store_id')
)->addForeignKey(
    $installer->getFkName('core_url_rewrite', 'store_id', 'store', 'store_id'),
    'store_id',
    $installer->getTable('store'),
    'store_id',
    \Magento\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Url Rewrites'
);
$installer->getConnection()->createTable($table);

$installer->endSetup();
