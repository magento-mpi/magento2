<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Setup\Module\SetupModule */
$installer = $this;

$installer->startSetup();

/**
 * Create table 'indexer_state'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('indexer_state')
)->addColumn(
    'state_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Indexer State Id'
)->addColumn(
    'indexer_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Indexer Id'
)->addColumn(
    'status',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    16,
    array('default' => 'invalid'),
    'Indexer Status'
)->addColumn(
    'updated',
    \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
    null,
    array(),
    'Indexer Status'
)->addIndex(
    $installer->getIdxName('indexer_state', array('indexer_id')),
    array('indexer_id')
)->setComment(
    'Indexer State'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'mview_state'
 */
$table = $installer->getConnection()
    ->newTable(
        $installer->getTable('mview_state')
    )->addColumn(
        'state_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary'  => true,
        ),
        'View State Id'
    )->addColumn(
        'view_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        255,
        array(),
        'View Id'
    )->addColumn(
        'mode',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        16,
        array('default' => 'disabled'),
        'View Mode'
    )->addColumn(
        'status',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        16,
        array('default' => 'idle'),
        'View Status'
    )->addColumn(
        'updated',
        \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
        null,
        array(),
        'View updated time'
    )->addColumn(
        'version_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        array('unsigned' => true,),
        'View Version Id'
    )->addIndex(
        $installer->getIdxName('mview_state', array('view_id')),
        array('view_id')
    )->addIndex(
        $installer->getIdxName('mview_state', array('mode')),
        array('mode')
    )->setComment('View State');
$installer->getConnection()->createTable($table);

$installer->endSetup();
