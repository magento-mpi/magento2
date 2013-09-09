<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_ImportExport_Model_Resource_Setup */
$installer = $this;

/**
 * Create table 'enterprise_scheduled_operations'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('enterprise_scheduled_operations'))
    ->addColumn('id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Id')
    ->addColumn('name', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        ), 'Operation Name')
    ->addColumn('operation_type', Magento_DB_Ddl_Table::TYPE_TEXT, 50, array(
        'nullable'  => false,
        ), 'Operation')
    ->addColumn('entity_type', Magento_DB_Ddl_Table::TYPE_TEXT, 50, array(
        'nullable'  => false,
        ), 'Entity')
    ->addColumn('behavior', Magento_DB_Ddl_Table::TYPE_TEXT, 15, array(
        'nullable'  => true
        ), 'Behavior')
    ->addColumn('start_time', Magento_DB_Ddl_Table::TYPE_TEXT, 10, array(
        'nullable'  => false,
        ), 'Start Time')
    ->addColumn('freq', Magento_DB_Ddl_Table::TYPE_TEXT, 1, array(
        'nullable'  => false,
        ), 'Frequency')
    ->addColumn('force_import', Magento_DB_Ddl_Table::TYPE_SMALLINT, 1, array(
        'nullable'  => false,
        ), 'Force Import')
    ->addColumn('file_info', Magento_DB_Ddl_Table::TYPE_TEXT, '64k', array(
        'nullable'  => true,
        ), 'File Information')
    ->addColumn('details', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        ), 'Operation Details')
    ->addColumn('entity_attributes', Magento_DB_Ddl_Table::TYPE_TEXT, '64k', array(
        'nullable'  => true,
        ), 'Entity Attributes')
    ->addColumn('status', Magento_DB_Ddl_Table::TYPE_SMALLINT, 1, array(
        'nullable'  => false,
        ), 'Status')
    ->addColumn('is_success', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => Magento_ScheduledImportExport_Model_Scheduled_Operation_Data::STATUS_PENDING
        ), 'Is Success')
    ->addColumn('last_run_date', Magento_DB_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => true,
        ), 'Last Run Date')
    ->addColumn('email_receiver', Magento_DB_Ddl_Table::TYPE_TEXT, 150, array(
        'nullable'  => false,
        ), 'Email Receiver')
    ->addColumn('email_sender', Magento_DB_Ddl_Table::TYPE_TEXT, 150, array(
        'nullable'  => false,
        ), 'Email Receiver')
    ->addColumn('email_template', Magento_DB_Ddl_Table::TYPE_TEXT, 250, array(
        'nullable'  => false,
        ), 'Email Template')
    ->addColumn('email_copy', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        ), 'Email Copy')
    ->addColumn('email_copy_method', Magento_DB_Ddl_Table::TYPE_TEXT, 10, array(
        'nullable'  => false,
        ), 'Email Copy Method')
    ->setComment('Scheduled Import/Export Table');
$installer->getConnection()->createTable($table);
