<?php
/**
 * {license_notice}
 *
 * @category    Varien
 * @package     Varien_Db
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = new Mage_Core_Model_Resource_Setup(Mage_Core_Model_Resource_Setup::DEFAULT_SETUP_CONNECTION);
$connection = $installer->getConnection();

$tableName = '_two_column_idx';
$table = $connection
    ->newTable($installer->getTable($tableName))
    ->addColumn('column1', Varien_Db_Ddl_Table::TYPE_INTEGER)
    ->addColumn('column2', Varien_Db_Ddl_Table::TYPE_INTEGER)
    ->addIndex($installer->getIdxName($tableName, array('column1')), array('column1'))
    ->addIndex($installer->getIdxName($tableName, array('column1', 'column2')), array('column1', 'column2'))
;
$connection->createTable($table);
