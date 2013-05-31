<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * Create table 'vde_theme_change'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('vde_theme_change'))
    ->addColumn('change_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary'  => true,
    ), 'Theme Change Identifier')
    ->addColumn('theme_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
        'unsigned' => true,
    ), 'Theme Id')
    ->addColumn('change_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array('nullable' => false), 'Change Time')
    ->addForeignKey(
        $installer->getFkName('vde_theme_change', 'theme_id', 'core_theme', 'theme_id'),
        'theme_id',
        $installer->getTable('core_theme'),
        'theme_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Design Editor Theme Change');

$installer->getConnection()->createTable($table);

$installer->endSetup();
