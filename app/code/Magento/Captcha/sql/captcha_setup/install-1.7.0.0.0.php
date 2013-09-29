<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Captcha
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Core\Model\Resource\Setup */
$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('captcha_log'))
    ->addColumn('type', \Magento\DB\Ddl\Table::TYPE_TEXT, 32, array(
        'nullable'  => false,
        'primary'   => true,
        ), 'Type')
    ->addColumn('value', \Magento\DB\Ddl\Table::TYPE_TEXT, 32, array(
        'nullable'  => false,
        'unsigned'  => true,
        'primary'   => true,
        ), 'Value')
    ->addColumn('count', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Count')
    ->addColumn('updated_at', \Magento\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(), 'Update Time')
    ->setComment('Count Login Attempts');
$installer->getConnection()->createTable($table);

$installer->endSetup();
