<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Mage_Launcher_Model_Resource_Setup */

$installer = $this;
$installer->startSetup();

/**
 * Create table 'launcher_page'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('launcher_page'))
    ->addColumn('page_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
    ), 'Id')
    ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
        'nullable'  => false,
    ), 'Page Code')
    // Page code has to be unique within the application
    ->addIndex($installer->getIdxName('launcher_page', array('code'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('code'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->setComment('Landing Page Data Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'launcher_tile'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('launcher_tile'))
    ->addColumn('tile_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
    ), 'Id')
    ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
        'nullable' => false,
    ), 'Tile Code')
    ->addColumn('page_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
    ), 'Tile Page')
    ->addColumn('state', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
    ), 'Tile State')
    ->addColumn('is_skippable', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned' => true,
        'nullable' => false,
        'default' => 1,
    ), 'Flag that shows whether tile can be skipped.')
    ->addColumn('is_dismissible', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned' => true,
        'nullable' => false,
        'default' => 1,
    ), 'Flag that shows whether tile can be dismissed.')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned' => true,
        'default' => 0,
    ), 'Sort order of the tile.')
    // Table indexes and constraints
    ->addIndex($installer->getIdxName('launcher_tile', array('page_id')),
        array('page_id'))
    ->addIndex($installer->getIdxName('launcher_tile', array('state')),
        array('state'))
    ->addIndex($installer->getIdxName('launcher_tile', array('sort_order')),
        array('sort_order'))
    // Tile code has to be unique within the application
    ->addIndex($installer->getIdxName('launcher_tile', array('code'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('code'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addForeignKey($installer->getFkName('launcher_page', 'page_id', 'launcher_tile', 'page_id'),
        'page_id', $installer->getTable('launcher_page'), 'page_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Tile Data Table');
$installer->getConnection()->createTable($table);

$installer->endSetup();
