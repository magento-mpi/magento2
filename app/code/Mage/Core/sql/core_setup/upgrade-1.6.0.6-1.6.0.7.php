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

$installer->endSetup();
