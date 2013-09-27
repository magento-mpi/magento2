<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sendfriend
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/* @var $installer Magento_Core_Model_Resource_Setup */

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('sendfriend_log'))
    ->addColumn('log_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Log ID')
    ->addColumn('ip', Magento_DB_Ddl_Table::TYPE_BIGINT, '20', array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Customer IP address')
    ->addColumn('time', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Log time')
    ->addColumn('website_id', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Website ID')
    ->addIndex($installer->getIdxName('sendfriend_log', 'ip'), 'ip')
    ->addIndex($installer->getIdxName('sendfriend_log', 'time'), 'time')
    ->setComment('Send to friend function log storage table');
$installer->getConnection()->createTable($table);

$installer->endSetup();
