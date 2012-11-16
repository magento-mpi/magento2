<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * Create table 'widget'
 */
if (!$installer->getConnection()->isTableExists($installer->getTable('widget'))) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('widget'))
        ->addColumn('widget_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            ), 'Widget Id')
        ->addColumn('widget_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            ), 'Widget code for template directive')
        ->addColumn('widget_type', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            ), 'Widget Type')
        ->addColumn('parameters', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
            'nullable'  => true,
            ), 'Parameters')
        ->addIndex($installer->getIdxName('widget', 'widget_code'), 'widget_code')
        ->setComment('Preconfigured Widgets');
    $installer->getConnection()->createTable($table);
} else {

    $installer->getConnection()->dropIndex(
        $installer->getTable('widget'),
        'IDX_CODE'
    );

    $tables = array(
        $installer->getTable('widget') => array(
            'columns' => array(
                'widget_id' => array(
                    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                    'identity'  => true,
                    'unsigned'  => true,
                    'nullable'  => false,
                    'primary'   => true,
                    'comment'   => 'Widget Id'
                ),
                'parameters' => array(
                    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                    'length'    => '64K',
                    'comment'   => 'Parameters'
                )
            ),
            'comment' => 'Preconfigured Widgets'
        )
    );

    $installer->getConnection()->modifyTables($tables);

    $installer->getConnection()->changeColumn(
        $installer->getTable('widget'),
        'code',
        'widget_code',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length'    => 255,
            'comment'   => 'Widget code for template directive'
        )
    );

    $installer->getConnection()->changeColumn(
        $installer->getTable('widget'),
        'type',
        'widget_type',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length'    => 255,
            'comment'   => 'Widget Type'
        )
    );

    $installer->getConnection()->addIndex(
        $installer->getTable('widget'),
        $installer->getIdxName('widget', array('widget_code')),
        array('widget_code')
    );
}

/**
 * Create table 'widget_instance'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('widget_instance'))
    ->addColumn('instance_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Instance Id')
    ->addColumn('instance_type', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Instance Type')
    ->addColumn('theme_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable'  => false
        ), 'Theme id')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Widget Title')
    ->addColumn('store_ids', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Store ids')
    ->addColumn('widget_parameters', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Widget parameters')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Sort order')
    ->addForeignKey($installer->getFkName('widget_instance', 'theme_id', 'core_theme', 'theme_id'),
        'theme_id', $installer->getTable('core_theme'), 'theme_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Instances of Widget for Package Theme');
$installer->getConnection()->createTable($table);

/**
 * Create table 'widget_instance_page'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('widget_instance_page'))
    ->addColumn('page_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Page Id')
    ->addColumn('instance_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Instance Id')
    ->addColumn('page_group', Varien_Db_Ddl_Table::TYPE_TEXT, 25, array(
        ), 'Block Group Type')
    ->addColumn('layout_handle', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Layout Handle')
    ->addColumn('block_reference', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Container')
    ->addColumn('page_for', Varien_Db_Ddl_Table::TYPE_TEXT, 25, array(
        ), 'For instance entities')
    ->addColumn('entities', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Catalog entities (comma separated)')
    ->addColumn('page_template', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Path to widget template')
    ->addIndex($installer->getIdxName('widget_instance_page', 'instance_id'), 'instance_id')
    ->addForeignKey($installer->getFkName('widget_instance_page', 'instance_id', 'widget_instance', 'instance_id'),
        'instance_id', $installer->getTable('widget_instance'), 'instance_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Instance of Widget on Page');
$installer->getConnection()->createTable($table);

/**
 * Create table 'widget_instance_page_layout'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('widget_instance_page_layout'))
    ->addColumn('page_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Page Id')
    ->addColumn('layout_update_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Layout Update Id')
    ->addIndex($installer->getIdxName('widget_instance_page_layout', 'page_id'), 'page_id')
    ->addIndex($installer->getIdxName('widget_instance_page_layout', 'layout_update_id'), 'layout_update_id')
    ->addIndex($installer->getIdxName('widget_instance_page_layout',
        array('layout_update_id', 'page_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('layout_update_id', 'page_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addForeignKey($installer->getFkName('widget_instance_page_layout', 'page_id', 'widget_instance_page', 'page_id'),
        'page_id', $installer->getTable('widget_instance_page'), 'page_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('widget_instance_page_layout', 'layout_update_id', 'core_layout_update', 'layout_update_id'),
        'layout_update_id', $installer->getTable('core_layout_update'), 'layout_update_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Layout updates');
$installer->getConnection()->createTable($table);

$installer->endSetup();
