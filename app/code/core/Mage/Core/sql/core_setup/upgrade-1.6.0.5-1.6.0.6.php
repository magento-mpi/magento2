<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright  {copyright}
 * @license    {license_link}
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();
$connection = $installer->getConnection();
$table = $installer->getTable('core_layout_link');

$connection->dropForeignKey(
    $table,
    $installer->getFkName('core_layout_link', 'store_id', 'core_store', 'store_id')
);

$connection->dropIndex($table, $installer->getIdxName(
    'core_layout_link',
    array('store_id', 'package', 'theme', 'layout_update_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
));

$connection->dropColumn($table, 'area');

$connection->dropColumn($table, 'package');

$connection->changeColumn($table, 'theme', 'theme_id', array(
    'type'     => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'unsigned' => true,
    'nullable' => false,
    'comment'  => 'Theme id'
));

$connection->addIndex($table, $installer->getIdxName(
    'core_layout_link',
    array('store_id', 'theme_id', 'layout_update_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
), array('store_id', 'theme_id', 'layout_update_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE);

$connection->addForeignKey(
    $installer->getFkName('core_layout_link', 'store_id', 'core_store', 'store_id'),
    $table,
    'store_id',
    $installer->getTable('core_store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_CASCADE
);

$connection->addForeignKey(
    $installer->getFkName('core_layout_link', 'theme_id', 'core_theme', 'theme_id'),
    $table,
    'theme_id',
    $installer->getTable('core_theme'),
    'theme_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_CASCADE
);

$installer->endSetup();
