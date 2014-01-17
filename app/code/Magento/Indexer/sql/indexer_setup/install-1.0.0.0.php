<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/* @var $installer \Magento\Core\Model\Resource\Setup */

$installer->startSetup();

/**
 * Create table 'indexer_state'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('indexer_state'))
    ->addColumn('state_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
    ), 'Indexer State Id')
    ->addColumn('indexer_id', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
    ), 'Indexer Id')
    ->addColumn('status', \Magento\DB\Ddl\Table::TYPE_TEXT, 16, array(
        'default' => \Magento\Indexer\Model\Indexer\State::STATUS_INVALID,
    ), 'Indexer Status')
    ->addColumn('updated', \Magento\DB\Ddl\Table::TYPE_DATETIME, null, array(
    ), 'Indexer Status')
    ->addIndex($installer->getIdxName('indexer_state', array('indexer_id')),
        array('indexer_id'))
    ->setComment('Indexer State');
$installer->getConnection()->createTable($table);

$installer->endSetup();
