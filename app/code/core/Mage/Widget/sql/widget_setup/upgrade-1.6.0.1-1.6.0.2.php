<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();
$connection = $installer->getConnection();
$table = $installer->getTable('widget_instance');

$connection->changeColumn($table, 'package_theme', 'theme_id', array(
    'type'     => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'unsigned' => true,
    'nullable' => false,
    'comment'  => 'Theme id'
));

$connection->addForeignKey(
    $table,
    $installer->getFkName('widget_instance', 'theme_id', 'core_theme', 'theme_id'),
    'theme_id',
    $installer->getTable('core_theme'),
    'theme_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_CASCADE
);

$installer->endSetup();
