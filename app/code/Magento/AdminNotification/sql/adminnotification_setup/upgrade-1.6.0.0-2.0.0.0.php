<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
$installer = $this;
/* @var $installer \Magento\Core\Model\Resource\Setup */

$installer->startSetup();
/**
 * Create table 'admin_system_messages'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('admin_system_messages'))
    ->addColumn('identity', \Magento\DB\Ddl\Table::TYPE_TEXT, 100, array(
        'nullable'  => false,
        'primary'   => true,
    ), 'Message id')
    ->addColumn('severity', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Problem type')
    ->addColumn('created_at', \Magento\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
    ), 'Create date')
    ->setComment('Admin System Messages');
$installer->getConnection()->createTable($table);

$installer->endSetup();
