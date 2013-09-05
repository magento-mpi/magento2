<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdminNotification
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * AdminNotification install
 *
 * @category    Magento
 * @package     Magento_AdminNotification
 * @author      Magento Core Team <core@magentocommerce.com>
 */
$installer = $this;
/* @var $installer Magento_Core_Model_Resource_Setup */

$installer->startSetup();
/**
 * Create table 'adminnotification_inbox'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('adminnotification_inbox'))
    ->addColumn('notification_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Notification id')
    ->addColumn('severity', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Problem type')
    ->addColumn('date_added', \Magento\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Create date')
    ->addColumn('title', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        ), 'Title')
    ->addColumn('description', \Magento\DB\Ddl\Table::TYPE_TEXT, '64k', array(
        ), 'Description')
    ->addColumn('url', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Url')
    ->addColumn('is_read', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Flag if notification read')
    ->addColumn('is_remove', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Flag if notification might be removed')
    ->addIndex($installer->getIdxName('adminnotification_inbox', array('severity')),
        array('severity'))
    ->addIndex($installer->getIdxName('adminnotification_inbox', array('is_read')),
        array('is_read'))
    ->addIndex($installer->getIdxName('adminnotification_inbox', array('is_remove')),
        array('is_remove'))
    ->setComment('Adminnotification Inbox');
$installer->getConnection()->createTable($table);

$installer->endSetup();
