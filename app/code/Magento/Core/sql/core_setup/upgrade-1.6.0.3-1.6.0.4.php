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

/**
 * Create table 'core_theme'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core_theme'))
    ->addColumn('theme_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary'  => true,
    ), 'Theme identifier')
    ->addColumn('parent_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array('nullable' => true), 'Parent Id')
    ->addColumn('theme_path', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array('nullable' => true), 'Theme Path')
    ->addColumn('theme_version', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array('nullable' => false), 'Theme Version')
    ->addColumn('theme_title', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array('nullable' => false), 'Theme Title')
    ->addColumn('preview_image', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array('nullable' => true), 'Preview Image')
    ->addColumn('magento_version_from', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false
    ), 'Magento Version From')
    ->addColumn('magento_version_to', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false
    ), 'Magento Version To')
    ->addColumn('is_featured', Magento_DB_Ddl_Table::TYPE_BOOLEAN, null, array(
        'nullable' => false,
        'default'  => 0
    ), 'Is Theme Featured')
    ->setComment('Core theme');

$installer->getConnection()->createTable($table);

$installer->endSetup();
