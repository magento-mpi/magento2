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

/**
 * Create table 'core_package'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core_package'))
    ->addColumn('package_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary'  => true,
    ), 'Package identifier')
    ->addColumn('package_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array('nullable' => false), 'Package Code')
    ->addColumn('package_title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array('nullable' => false), 'Package Title')
    ->setComment('Core package');

$installer->getConnection()->createTable($table);

/**
 * Create table 'core_theme'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core_theme'))
    ->addColumn('theme_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary'  => true,
    ), 'Theme identifier')
    ->addColumn('package_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
    ), 'Package identifier')
    ->addColumn('parent_theme', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array('nullable' => true), 'Parent Theme')
    ->addColumn('theme_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array('nullable' => false), 'Theme Code')
    ->addColumn('theme_version', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array('nullable' => false), 'Theme Version')
    ->addColumn('theme_title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array('nullable' => false), 'Theme Title')
    ->addColumn('magento_version_from', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false
    ), 'Magento Version From')
    ->addColumn('magento_version_to', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false
    ), 'Magento Version To')
    ->addForeignKey($installer->getFkName('core_theme', 'package_id', 'core_package', 'package_id'),
        'package_id', $installer->getTable('core_package'), 'package_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Core theme');

$installer->getConnection()->createTable($table);

$installer->endSetup();
