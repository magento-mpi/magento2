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

/**
 * Modifying 'core_layout_link' table. Adding 'is_temporary' column
 */
$tableCoreLayoutLink = $installer->getTable('core_layout_link');

$connection->addColumn($tableCoreLayoutLink, 'is_temporary',
    array(
        'type'     => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
        'nullable' => false,
        'default'  => '0',
        'comment'  => 'Defines whether Layout Update is Temporary'
    )
);

// we must drop next 2 foreign keys to have an ability to drop index
$connection->dropForeignKey(
    $tableCoreLayoutLink,
    $installer->getFkName($tableCoreLayoutLink, 'store_id', 'core_store', 'store_id')
);
$connection->dropForeignKey(
    $tableCoreLayoutLink,
    $installer->getFkName($tableCoreLayoutLink, 'theme_id', 'core_theme', 'theme_id')
);

$connection->dropIndex($tableCoreLayoutLink, $installer->getIdxName(
    $tableCoreLayoutLink,
    array('store_id', 'theme_id', 'layout_update_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
));

$connection->addIndex($tableCoreLayoutLink,
    $installer->getIdxName(
        $tableCoreLayoutLink,
        array('store_id', 'theme_id', 'layout_update_id', 'is_temporary'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('store_id', 'theme_id', 'layout_update_id', 'is_temporary'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

// recreate 2 dropped foreign keys to have an ability to drop index
$connection->addForeignKey(
    $installer->getFkName($tableCoreLayoutLink, 'store_id', 'core_store', 'store_id'),
    $tableCoreLayoutLink,
    'store_id',
    $installer->getTable('core_store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_CASCADE
);
$connection->addForeignKey(
    $installer->getFkName($tableCoreLayoutLink, 'theme_id', 'core_theme', 'theme_id'),
    $tableCoreLayoutLink,
    'theme_id',
    $installer->getTable('core_theme'),
    'theme_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_CASCADE
);

/**
 * Create table 'core_theme_files'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core_theme_files'))
    ->addColumn('theme_files_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary'  => true,
    ), 'Theme files identifier')
    ->addColumn('theme_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
        'unsigned' => true
    ), 'Theme Id')
    ->addColumn('file_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array('nullable' => false), 'File Name')
    ->addColumn('file_type', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array('nullable' => false), 'File Type')
    ->addColumn('content', Varien_Db_Ddl_Table::TYPE_TEXT, Varien_Db_Ddl_Table::MAX_TEXT_SIZE, array(
        'nullable' => false
    ), 'File Content')
    ->addColumn('order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('nullable' => false, 'default'  => 0), 'Order')
    ->addForeignKey(
        $installer->getFkName('core_theme_files', 'theme_id', 'core_theme', 'theme_id'),
        'theme_id',
        $installer->getTable('core_theme'),
        'theme_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Core theme files');

$installer->getConnection()->createTable($table);

$installer->endSetup();
