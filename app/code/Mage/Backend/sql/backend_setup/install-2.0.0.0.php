<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();
/**
 * Create table 'admin_system_messages'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('admin_system_messages'))
    ->addColumn('message_id', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Message id')
    ->addColumn('severity', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Problem type')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
    ), 'Create date')
    ->setComment('Adminnotification Inbox');
$installer->getConnection()->createTable($table);

$installer->endSetup();
