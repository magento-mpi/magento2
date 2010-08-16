<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Index
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Index_Model_Resource_Setup */

$installer->startSetup();

/**
 * Create table 'index/event'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('index/event'))
    ->addColumn('event_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'event id')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
        'nullable'  => false,
        ), 'type')
    ->addColumn('entity', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
        'nullable'  => false,
        ), 'entity')
    ->addColumn('entity_pk', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
        ), 'entity primary key')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'creation time')
    ->addColumn('old_data', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
        ), 'old data')
    ->addColumn('new_data', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
        ), 'new data')
    ->addIndex($installer->getIdxName('index/event', array('type', 'entity', 'entity_pk'), true),
        array('type', 'entity', 'entity_pk'), array('unique' => true))
    ->setComment('index event');
$installer->getConnection()->createTable($table);

/**
 * Create table 'index/process'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('index/process'))
    ->addColumn('process_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'process id')
    ->addColumn('indexer_code', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable'  => false,
        ), 'indexer code')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 15, array(
        'nullable'  => false,
        'default'   => 'pending',
        ), 'status')
    ->addColumn('started_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'started at')
    ->addColumn('ended_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'ended at')
    ->addColumn('mode', Varien_Db_Ddl_Table::TYPE_TEXT, 9, array(
        'nullable'  => false,
        'default'   => 'real_time',
        ), 'mode')
    ->addIndex($installer->getIdxName('index/process', array('indexer_code'), true),
        array('indexer_code'), array('unique' => true))
    ->setComment('index process');
$installer->getConnection()->createTable($table);

/**
 * Create table 'index/process_event'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('index/process_event'))
    ->addColumn('process_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'process id')
    ->addColumn('event_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'event id')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 7, array(
        'nullable'  => false,
        'default'   => 'new',
        ), 'status')
    ->addIndex($installer->getIdxName('index/process_event', array('event_id')),
        array('event_id'))
    ->addForeignKey($installer->getFkName('index/process_event', 'event_id', 'index/event', 'event_id'),
        'event_id', $installer->getTable('index/event'), 'event_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('index/process_event', 'process_id', 'index/process', 'process_id'),
        'process_id', $installer->getTable('index/process'), 'process_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('index process event');
$installer->getConnection()->createTable($table);

$installer->endSetup();
