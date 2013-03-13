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
 * Rename table
 */
$wrongName = 'core_theme_files_link';
$rightName = 'core_theme_file_update';
if ($installer->tableExists($wrongName)) {
    $connection->renameTable($installer->getTable($wrongName), $installer->getTable($rightName));
}

$tableName = $installer->getTable($rightName);

/**
 * Rename column
 */
$wrongColumn = 'files_link_id';
$rightColumn = 'file_update_id';
$connection->changeColumn($tableName, $wrongColumn, $rightColumn, array(
    'type'     => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'identity' => true,
    'primary'  => true,
    'nullable' => false,
    'unsigned' => true,
    'comment'  => 'Customization file update id'
));

/**
 * Rename column
 */
$wrongColumn = 'layout_link_id';
$rightColumn = 'layout_update_id';
$connection->changeColumn($tableName, $wrongColumn, $rightColumn, array(
    'type'     => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'nullable' => false,
    'unsigned' => true,
    'comment'  => 'Theme layout update id'
));

/**
 * Drop foreign key and index
 */
$connection->dropForeignKey(
    $tableName,
    $installer->getFkName($wrongName, 'theme_id', 'core_theme', 'theme_id')
);
$connection->dropIndex(
    $tableName,
    $installer->getFkName($wrongName, 'theme_id', 'core_theme', 'theme_id')
);

/**
 * Add foreign keys and indexes
 */
$connection->addIndex(
    $tableName,
    $installer->getIdxName($tableName, 'theme_id', Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
    'theme_id',
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);
$connection->addForeignKey(
    $installer->getFkName($tableName, 'theme_id', 'core_theme', 'theme_id'),
    $tableName,
    'theme_id',
    $installer->getTable('core_theme'),
    'theme_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
);
$connection->addIndex(
    $tableName,
    $installer->getIdxName($tableName, 'layout_update_id', Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX),
    'layout_update_id',
    Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
);
$connection->addForeignKey(
    $installer->getFkName($tableName, 'layout_update_id', 'core_layout_update', 'layout_update_id'),
    $tableName,
    'layout_update_id',
    $installer->getTable('core_layout_update'),
    'layout_update_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
);

/**
 * Change data
 */
$select = $connection->select()
    ->from($tableName)
    ->join(
        array('link' => $installer->getTable('core_layout_link')),
        sprintf('link.layout_link_id = %s.layout_update_id', $tableName)
    );
$rows = $connection->fetchAll($select);
foreach ($rows as $row) {
    $connection->update(
        $tableName,
        array('layout_update_id' => $row['layout_update_id']),
        'file_update_id = ' . $row['file_update_id']
    );
}

$installer->endSetup();
