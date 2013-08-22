<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Poll
 * @copyright   {copyright}
 * @license     {license_link}
 */


/* @var $installer Magento_Core_Model_Resource_Setup */

$installer = $this;

$installer->startSetup();

/**
 * Create table 'poll'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('poll'))
    ->addColumn('poll_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Poll Id')
    ->addColumn('poll_title', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Poll title')
    ->addColumn('votes_count', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Votes Count')
    ->addColumn('store_id', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store id')
    ->addColumn('date_posted', Magento_DB_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Date posted')
    ->addColumn('date_closed', Magento_DB_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => true,
        ), 'Date closed')
    ->addColumn('active', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '1',
        ), 'Is active')
    ->addColumn('closed', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Is closed')
    ->addColumn('answers_display', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => true,
        ), 'Answers display')
    ->addIndex($installer->getIdxName('poll', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('poll', 'store_id', 'core_store', 'store_id'),
        'store_id', $installer->getTable('core_store'), 'store_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE,
        Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->setComment('Poll');
$installer->getConnection()->createTable($table);

/**
 * Create table 'poll_answer'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('poll_answer'))
    ->addColumn('answer_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Answer Id')
    ->addColumn('poll_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Poll Id')
    ->addColumn('answer_title', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Answer title')
    ->addColumn('votes_count', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Votes Count')
    ->addColumn('answer_order', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Answers display')
    ->addIndex($installer->getIdxName('poll_answer', array('poll_id')),
        array('poll_id'))
    ->addForeignKey($installer->getFkName('poll_answer', 'poll_id', 'poll', 'poll_id'),
        'poll_id', $installer->getTable('poll'), 'poll_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->setComment('Poll Answers');
$installer->getConnection()->createTable($table);

/**
 * Create table 'poll_store'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('poll_store'))
    ->addColumn('poll_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'primary'   => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Poll Id')
    ->addColumn('store_id', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'primary'   => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store id')
    ->addIndex($installer->getIdxName('poll_store', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('poll_store', 'poll_id', 'poll', 'poll_id'),
        'poll_id', $installer->getTable('poll'), 'poll_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('poll_store', 'store_id', 'core_store', 'store_id'),
        'store_id', $installer->getTable('core_store'), 'store_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->setComment('Poll Store');
$installer->getConnection()->createTable($table);

/**
 * Create table 'poll_vote'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('poll_vote'))
    ->addColumn('vote_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Vote Id')
    ->addColumn('poll_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Poll Id')
    ->addColumn('poll_answer_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Poll answer id')
    ->addColumn('ip_address', Magento_DB_Ddl_Table::TYPE_BIGINT, null, array(
        'nullable'  => true,
        ), 'Poll answer id')
    ->addColumn('customer_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => true,
        ), 'Customer id')
    ->addColumn('vote_time', Magento_DB_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => true,
        ), 'Date closed')
    ->addIndex($installer->getIdxName('poll_vote', array('poll_answer_id')),
        array('poll_answer_id'))
    ->addForeignKey($installer->getFkName('poll_vote', 'poll_answer_id', 'poll_answer', 'answer_id'),
        'poll_answer_id', $installer->getTable('poll_answer'), 'answer_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE,
        Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->setComment('Poll Vote');
$installer->getConnection()->createTable($table);

$installer->endSetup();
