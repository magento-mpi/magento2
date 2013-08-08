<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright  {copyright}
 * @license    {license_link}
 */

/* @var $installer Magento_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();
$connection = $installer->getConnection();

/**
 * Modifying 'core_layout_link' table. Replace columns area, package, theme to theme_id
 */
$tableCoreLayoutLink = $installer->getTable('core_layout_link');

$connection->dropForeignKey(
    $tableCoreLayoutLink,
    $installer->getFkName('core_layout_link', 'store_id', 'core_store', 'store_id')
);

$connection->dropIndex($tableCoreLayoutLink, $installer->getIdxName(
    'core_layout_link',
    array('store_id', 'package', 'theme', 'layout_update_id'),
    Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE
));

$connection->dropColumn($tableCoreLayoutLink, 'area');

$connection->dropColumn($tableCoreLayoutLink, 'package');

$connection->changeColumn($tableCoreLayoutLink, 'theme', 'theme_id', array(
    'type'     => Magento_DB_Ddl_Table::TYPE_INTEGER,
    'unsigned' => true,
    'nullable' => false,
    'comment'  => 'Theme id'
));

$connection->addIndex($tableCoreLayoutLink, $installer->getIdxName(
    'core_layout_link',
    array('store_id', 'theme_id', 'layout_update_id'),
    Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE
), array('store_id', 'theme_id', 'layout_update_id'), Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE);

$connection->addForeignKey(
    $installer->getFkName('core_layout_link', 'store_id', 'core_store', 'store_id'),
    $tableCoreLayoutLink,
    'store_id',
    $installer->getTable('core_store'),
    'store_id',
    Magento_DB_Ddl_Table::ACTION_CASCADE,
    Magento_DB_Ddl_Table::ACTION_CASCADE
);

$connection->addForeignKey(
    $installer->getFkName('core_layout_link', 'theme_id', 'core_theme', 'theme_id'),
    $tableCoreLayoutLink,
    'theme_id',
    $installer->getTable('core_theme'),
    'theme_id',
    Magento_DB_Ddl_Table::ACTION_CASCADE,
    Magento_DB_Ddl_Table::ACTION_CASCADE
);

/**
 * Add column 'area' to 'core_theme'
 */
$connection->addColumn($installer->getTable('core_theme'), 'area', array(
    'type'     => Magento_DB_Ddl_Table::TYPE_TEXT,
    'length'   => '255',
    'nullable' => false,
    'comment'  => 'Theme Area'
));

$installer->endSetup();
