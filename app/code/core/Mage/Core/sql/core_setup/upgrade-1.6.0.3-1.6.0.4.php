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
    ->addColumn('package_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array('nullable' => false), 'Package Code')
    ->addColumn('package_title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array('nullable' => false), 'Package Title')
    ->addColumn('parent_theme', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array('nullable' => true), 'Parent Theme')
    ->addColumn('theme_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array('nullable' => false), 'Theme Code')
    ->addColumn('theme_version', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array('nullable' => false), 'Theme Version')
    ->addColumn('theme_title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array('nullable' => false), 'Theme Title')
    ->addColumn('magento_version_from', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false
    ), 'Magento Version From')
    ->addColumn('magento_version_to', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false
    ), 'Magento Version To');

$installer->getConnection()->createTable($table);

$installer->endSetup();
