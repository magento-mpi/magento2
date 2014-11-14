<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Setup\Module\SetupModule */
$installer = $this;

/**
 * Create table 'magento_scheduled_operations'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_scheduled_operations')
)->addColumn(
    'id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Id'
)->addColumn(
    'name',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array('nullable' => false),
    'Operation Name'
)->addColumn(
    'operation_type',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    50,
    array('nullable' => false),
    'Operation'
)->addColumn(
    'entity_type',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    50,
    array('nullable' => false),
    'Entity'
)->addColumn(
    'behavior',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    15,
    array('nullable' => true),
    'Behavior'
)->addColumn(
    'start_time',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    10,
    array('nullable' => false),
    'Start Time'
)->addColumn(
    'freq',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    1,
    array('nullable' => false),
    'Frequency'
)->addColumn(
    'force_import',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    1,
    array('nullable' => false),
    'Force Import'
)->addColumn(
    'file_info',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '64k',
    array('nullable' => true),
    'File Information'
)->addColumn(
    'details',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array('nullable' => true),
    'Operation Details'
)->addColumn(
    'entity_attributes',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '64k',
    array('nullable' => true),
    'Entity Attributes'
)->addColumn(
    'status',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    1,
    array('nullable' => false),
    'Status'
)->addColumn(
    'is_success',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array(
        'nullable' => false,
        'default' => 2
    ),
    'Is Success'
)->addColumn(
    'last_run_date',
    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
    null,
    array('nullable' => true),
    'Last Run Date'
)->addColumn(
    'email_receiver',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    150,
    array('nullable' => false),
    'Email Receiver'
)->addColumn(
    'email_sender',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    150,
    array('nullable' => false),
    'Email Receiver'
)->addColumn(
    'email_template',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    250,
    array('nullable' => false),
    'Email Template'
)->addColumn(
    'email_copy',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array('nullable' => true),
    'Email Copy'
)->addColumn(
    'email_copy_method',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    10,
    array('nullable' => false),
    'Email Copy Method'
)->setComment(
    'Scheduled Import/Export Table'
);
$installer->getConnection()->createTable($table);
