<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Core\Model\Resource\Setup */
$installer = $this;
/**
 * Create table 'magento_logging_event'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_logging_event'))
    ->addColumn('log_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Log Id')
    ->addColumn('ip', \Magento\DB\Ddl\Table::TYPE_BIGINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Ip address')
    ->addColumn('x_forwarded_ip', \Magento\DB\Ddl\Table::TYPE_BIGINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Real ip address if visitor used proxy')
    ->addColumn('event_code', \Magento\DB\Ddl\Table::TYPE_TEXT, 100, array(
        ), 'Event Code')
    ->addColumn('time', \Magento\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(
        ), 'Even date')
    ->addColumn('action', \Magento\DB\Ddl\Table::TYPE_TEXT, 20, array(
        ), 'Event action')
    ->addColumn('info', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Additional information')
    ->addColumn('status', \Magento\DB\Ddl\Table::TYPE_TEXT, 15, array(
        ), 'Status')
    ->addColumn('user', \Magento\DB\Ddl\Table::TYPE_TEXT, 40, array(
        ), 'User name')
    ->addColumn('user_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'User Id')
    ->addColumn('fullaction', \Magento\DB\Ddl\Table::TYPE_TEXT, 200, array(
        ), 'Full action description')
    ->addColumn('error_message', \Magento\DB\Ddl\Table::TYPE_TEXT, '64k', array(
        ), 'Error Message')
    ->addIndex($installer->getIdxName('magento_logging_event', array('user_id')),
        array('user_id'))
    ->addIndex($installer->getIdxName('magento_logging_event', array('user')),
        array('user'))
    ->addForeignKey($installer->getFkName('magento_logging_event', 'user_id', 'admin_user', 'user_id'),
        'user_id', $installer->getTable('admin_user'), 'user_id',
        \Magento\DB\Ddl\Table::ACTION_SET_NULL, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->setComment('Enterprise Logging Event');
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_logging_event_changes'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_logging_event_changes'))
    ->addColumn('id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Enterprise logging id')
    ->addColumn('source_name', \Magento\DB\Ddl\Table::TYPE_TEXT, 150, array(
        ), 'Logged Source Name')
    ->addColumn('event_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        ), 'Logged event id')
    ->addColumn('source_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        ), 'Logged Source Id')
    ->addColumn('original_data', \Magento\DB\Ddl\Table::TYPE_TEXT, '64k', array(
        ), 'Logged Original Data')
    ->addColumn('result_data', \Magento\DB\Ddl\Table::TYPE_TEXT, '64k', array(
        ), 'Logged Result Data')
    ->addIndex($installer->getIdxName('magento_logging_event_changes', array('event_id')),
        array('event_id'))
    ->addForeignKey(
        $installer->getFkName('magento_logging_event_changes', 'event_id', 'magento_logging_event', 'log_id'),
        'event_id', $installer->getTable('magento_logging_event'), 'log_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE
    )
    ->setComment('Enterprise Logging Event Changes');
$installer->getConnection()->createTable($table);
