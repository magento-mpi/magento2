<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Module\Setup */
$installer = $this;

$installer->startSetup();
$connection = $installer->getConnection();
$connection->addColumn(
    $installer->getTable('admin_role'),
    'user_type',
    array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'length' => 16, 'nullable' => true, 'comment' => 'User type')
);
$connection->dropColumn($installer->getTable('admin_rule'), 'role_type');
$installer->endSetup();
