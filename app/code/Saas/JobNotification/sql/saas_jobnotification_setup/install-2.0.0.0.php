<?php
/**
 * Job Notification install
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
/**
 * Create table 'saas_jobnotification_inbox'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('saas_jobnotification_inbox'))
    ->addColumn('notification_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Message id')
    ->addColumn('date_added', Magento_DB_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        'default' => Magento_DB_Ddl_Table::TIMESTAMP_INIT,
        ), 'Create date')
    ->addColumn('title', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        ), 'Title')
    ->addColumn('is_read', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Flag if notification read')
    ->addColumn('is_remove', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Flag if notification might be removed')
    ->addIndex($installer->getIdxName('saas_jobnotification_inbox', array('is_read')),
        array('is_read'))
    ->addIndex($installer->getIdxName('saas_jobnotification_inbox', array('is_remove')),
        array('is_remove'))
    ->setComment('Job Notification Inbox');
$installer->getConnection()->createTable($table);

$installer->endSetup();
