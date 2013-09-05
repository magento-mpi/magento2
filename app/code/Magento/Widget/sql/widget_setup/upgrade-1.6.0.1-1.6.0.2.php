<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Magento_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();
$connection = $installer->getConnection();
$table = $installer->getTable('widget_instance');

$connection->changeColumn($table, 'package_theme', 'theme_id', array(
    'type'     => \Magento\DB\Ddl\Table::TYPE_INTEGER,
    'unsigned' => true,
    'nullable' => false,
    'comment'  => 'Theme id'
));

$connection->addForeignKey(
    $installer->getFkName('widget_instance', 'theme_id', 'core_theme', 'theme_id'),
    $table,
    'theme_id',
    $installer->getTable('core_theme'),
    'theme_id',
    \Magento\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\DB\Ddl\Table::ACTION_CASCADE
);

$installer->endSetup();
