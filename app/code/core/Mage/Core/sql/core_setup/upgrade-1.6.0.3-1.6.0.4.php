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
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('nullable' => true), 'Parent Id')
    ->addColumn('theme_path', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array('nullable' => false), 'Theme Path')
    ->addColumn('theme_version', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array('nullable' => false), 'Theme Version')
    ->addColumn('theme_title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array('nullable' => false), 'Theme Title')
    ->addColumn('preview_image', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array('nullable' => false), 'Preview Image')
    ->addColumn('magento_version_from', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false
    ), 'Magento Version From')
    ->addColumn('magento_version_to', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false
    ), 'Magento Version To')
    ->setComment('Core theme');

$installer->getConnection()->createTable($table);

$installer->endSetup();
