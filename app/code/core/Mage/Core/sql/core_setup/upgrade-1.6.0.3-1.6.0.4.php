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

/**
 * Create table 'core_layout_update_context'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core_layout_update_context'))
    ->addColumn('context_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary'  => true,
    ), 'Layout Update Context Identifier')
    ->addColumn('layout_update_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
    ), 'Layout Update Identifier')
    ->addColumn('entity_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array('nullable' => false), 'Context Entity Name')
    ->addColumn('entity_type', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array('nullable' => false), 'Context Entity Type')
    ->addColumn('value_varchar', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => true,
        'default'  => null
    ), 'Entity Value Varchar')
    ->addColumn('value_int', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => true,
        'unsigned' => false,
        'default'  => null
    ), 'Entity Value Integer')
    ->addColumn('value_datetime', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable' => true,
        'default' => null
    ), 'Entity Value Datetime')
    ->addColumn('relation_count', Varien_Db_Ddl_Table::TYPE_SMALLINT, 6, array(
        'nullable' => false,
        'unsigned' => true
    ), 'Context Entity Relation Count')
    ->addColumn('relation_hash', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable' => false,
    ), 'Context Entity Relation Hash')
    ->addIndex($installer->getIdxName('core_layout_update_context', array('entity_name', 'entity_type')),
        array('entity_name', 'entity_type'))
    ->addIndex(
        $installer->getIdxName(
            'core_layout_update_context',
            array('entity_name', 'relation_hash'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('entity_name', 'relation_hash'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    ->addIndex($installer->getIdxName('core_layout_update_context', array('relation_count')), array('relation_count'))
    ->addIndex($installer->getIdxName('core_layout_update_context', array('relation_hash')), array('relation_hash'))
    ->addForeignKey($installer->getFkName(
        'core_layout_update_context', 'layout_update_id', 'core_layout_update', 'layout_update_id'
    ), 'layout_update_id', $installer->getTable('core_layout_update'), 'layout_update_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Core Layout Update Context');

$installer->getConnection()->createTable($table);

$installer->endSetup();
