<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Magento\Setup\Module\SetupModule */
$installer = $this;

$installer->startSetup();

/**
 * Create table 'cron_schedule'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('cron_schedule')
)->addColumn(
    'schedule_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Schedule Id'
)->addColumn(
    'job_code',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array('nullable' => false, 'default' => '0'),
    'Job Code'
)->addColumn(
    'status',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    7,
    array('nullable' => false, 'default' => 'pending'),
    'Status'
)->addColumn(
    'messages',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '64k',
    array(),
    'Messages'
)->addColumn(
    'created_at',
    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
    null,
    array('nullable' => false),
    'Created At'
)->addColumn(
    'scheduled_at',
    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
    null,
    array('nullable' => true),
    'Scheduled At'
)->addColumn(
    'executed_at',
    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
    null,
    array('nullable' => true),
    'Executed At'
)->addColumn(
    'finished_at',
    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
    null,
    array('nullable' => true),
    'Finished At'
)->addIndex(
    $installer->getIdxName('cron_schedule', array('job_code')),
    array('job_code')
)->addIndex(
    $installer->getIdxName('cron_schedule', array('scheduled_at', 'status')),
    array('scheduled_at', 'status')
)->setComment(
    'Cron Schedule'
);
$installer->getConnection()->createTable($table);

$installer->endSetup();
