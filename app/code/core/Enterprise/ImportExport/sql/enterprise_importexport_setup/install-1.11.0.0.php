<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Mage_ImportExport_Model_Resource_Setup */
$installer = $this;

/**
 * Create table 'enterprise_scheduled_operations'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('enterprise_scheduled_operations'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Id')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        ), 'Operation Name')
    ->addColumn('operation_type', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
        'nullable'  => false,
        ), 'Operation')
    ->addColumn('entity_type', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
        'nullable'  => false,
        ), 'Entity')
    ->addColumn('behavior', Varien_Db_Ddl_Table::TYPE_TEXT, 15, array(
        'nullable'  => true
        ), 'Behavior')
    ->addColumn('start_time', Varien_Db_Ddl_Table::TYPE_TEXT, 10, array(
        'nullable'  => false,
        ), 'Start Time')
    ->addColumn('freq', Varien_Db_Ddl_Table::TYPE_TEXT, 1, array(
        'nullable'  => false,
        ), 'Frequency')
    ->addColumn('force_import', Varien_Db_Ddl_Table::TYPE_SMALLINT, 1, array(
        'nullable'  => false,
        ), 'Force Import')
    ->addColumn('file_info', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        'nullable'  => true,
        ), 'File Information')
    ->addColumn('details', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        ), 'Operation Details')
    ->addColumn('entity_attributes', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        'nullable'  => true,
        ), 'Entity Attributes')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, 1, array(
        'nullable'  => false,
        ), 'Status')
    ->addColumn('is_success', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => Enterprise_ImportExport_Model_Scheduled_Operation_Data::STATUS_PENDING
        ), 'Is Success')
    ->addColumn('last_run_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => true,
        ), 'Last Run Date')
    ->addColumn('email_receiver', Varien_Db_Ddl_Table::TYPE_TEXT, 150, array(
        'nullable'  => false,
        ), 'Email Receiver')
    ->addColumn('email_sender', Varien_Db_Ddl_Table::TYPE_TEXT, 150, array(
        'nullable'  => false,
        ), 'Email Receiver')
    ->addColumn('email_template', Varien_Db_Ddl_Table::TYPE_TEXT, 250, array(
        'nullable'  => false,
        ), 'Email Template')
    ->addColumn('email_copy', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        ), 'Email Copy')
    ->addColumn('email_copy_method', Varien_Db_Ddl_Table::TYPE_TEXT, 10, array(
        'nullable'  => false,
        ), 'Email Copy Method')
    ->setComment('Scheduled Import/Export Table');
$installer->getConnection()->createTable($table);
