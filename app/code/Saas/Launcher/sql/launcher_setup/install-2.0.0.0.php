<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Saas_Launcher_Model_Resource_Setup */

$installer = $this;
$installer->startSetup();

/**
 * Create table 'launcher_page'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('launcher_page'))
    ->addColumn('page_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
    ), 'Id')
    ->addColumn('page_code', Magento_DB_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable'  => false,
    ), 'Page Code')
    // Page code has to be unique within the application
    ->addIndex(
        $installer->getIdxName('launcher_page', array('page_code'), Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('page_code'),
        array('type' => Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    ->setComment('Landing Page Data Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'launcher_tile'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('launcher_tile'))
    ->addColumn('tile_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
    ), 'Id')
    ->addColumn('page_code', Magento_DB_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable' => false,
    ), 'Page Code')
    ->addColumn('tile_code', Magento_DB_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable' => false,
    ), 'Tile Code')
    ->addColumn('state', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
    ), 'Tile State')
    ->addColumn('is_skippable', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned' => true,
        'nullable' => false,
        'default' => 1,
    ), 'Flag that shows whether tile can be skipped.')
    ->addColumn('is_dismissible', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned' => true,
        'nullable' => false,
        'default' => 1,
    ), 'Flag that shows whether tile can be dismissed.')
    ->addColumn('sort_order', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned' => true,
        'default' => 0,
    ), 'Sort order of the tile.')
    // Table indexes and constraints
    ->addIndex($installer->getIdxName('launcher_tile', array('sort_order')),
        array('sort_order'))
    // tile_code has to be unique within the application
    ->addIndex(
        $installer->getIdxName(
            'launcher_tile',
            array('tile_code'),
            Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('tile_code'),
        array('type' => Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    ->addIndex(
        $installer->getIdxName(
            'launcher_tile',
            array('page_code', 'tile_code'),
            Magento_DB_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        array('page_code', 'tile_code'),
        array('type' => Magento_DB_Adapter_Interface::INDEX_TYPE_INDEX)
    )
    ->addForeignKey(
        $installer->getFkName('launcher_page', 'page_code', 'launcher_tile', 'page_code'),
        'page_code', $installer->getTable('launcher_page'), 'page_code',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Tile Data Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'launcher_link_tracker'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('launcher_link_tracker'))
    ->addColumn('link_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
    ), 'Id')
    ->addColumn('code', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false,
    ), 'Link Code')
    ->addColumn('url', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false,
    ), 'Link Url')
    ->addColumn('params', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
    ), 'Link params')
    ->addColumn('is_visited', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned' => true,
        'nullable' => false,
        'default' => 0,
    ), 'Is Link Visited?')
    ->addIndex($installer->getIdxName('launcher_link_tracker', array('code'),
        Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('code'), array('type' => Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->setComment('Link Tracker Data Table');
$installer->getConnection()->createTable($table);


$installer->endSetup();
