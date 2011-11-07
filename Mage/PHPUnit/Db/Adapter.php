<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Local DB adapter.
 * Needed to implement interface methods.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Db_Adapter extends Mage_PHPUnit_Db_Adapter_Abstract
    implements Varien_Db_Adapter_Interface
{
    /**
     * Empty method
     *
     * @param unknown_type $tableName
     * @param unknown_type $schemaName
     */
    public function describeTable($tableName, $schemaName = null)
    {
        // empty code

    }
    /**
     * Empty method
     *
     * @param unknown_type $tableName
     * @param unknown_type $schemaName
     * @return Varien_Db_Ddl_Table
     */
    public function newTable($tableName = null, $schemaName = null)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param Varien_Db_Ddl_Table $table
     * @return Zend_Db_Statement_Interface
     */
    public function createTable(Varien_Db_Ddl_Table $table)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $tableName
     * @param unknown_type $schemaName
     */
    public function dropTable($tableName, $schemaName = null)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $tableName
     * @param unknown_type $schemaName
     * @return Varien_Db_Adapter_Interface
     */
    public function truncateTable($tableName, $schemaName = null)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $tableName
     * @param unknown_type $schemaName
     */
    public function isTableExists($tableName, $schemaName = null)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $tableName
     * @param unknown_type $schemaName
     */
    public function showTableStatus($tableName, $schemaName = null)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $tableName
     * @param unknown_type $newTableName
     * @return Varien_Db_Ddl_Table
     */
    public function createTableByDdl($tableName, $newTableName)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $tableName
     * @param unknown_type $columnName
     * @param unknown_type $definition
     * @param unknown_type $flushData
     * @param unknown_type $schemaName
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function modifyColumnByDdl($tableName, $columnName, $definition, $flushData = false, $schemaName = null)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $oldTableName
     * @param unknown_type $newTableName
     * @param unknown_type $schemaName
     */
    public function renameTable($oldTableName, $newTableName, $schemaName = null)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $tableName
     * @param unknown_type $columnName
     * @param unknown_type $definition
     * @param unknown_type $schemaName
     * @return Varien_Db_Adapter_Interface
     */
    public function addColumn($tableName, $columnName, $definition, $schemaName = null)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $tableName
     * @param unknown_type $oldColumnName
     * @param unknown_type $newColumnName
     * @param unknown_type $definition
     * @param unknown_type $flushData
     * @param unknown_type $schemaName
     * @return Varien_Db_Adapter_Interface
     */
    public function changeColumn($tableName, $oldColumnName, $newColumnName, $definition, $flushData = false, $schemaName = null)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $tableName
     * @param unknown_type $columnName
     * @param unknown_type $definition
     * @param unknown_type $flushData
     * @param unknown_type $schemaName
     * @return Varien_Db_Adapter_Interface
     */
    public function modifyColumn($tableName, $columnName, $definition, $flushData = false, $schemaName = null)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $tableName
     * @param unknown_type $columnName
     * @param unknown_type $schemaName
     */
    public function dropColumn($tableName, $columnName, $schemaName = null)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $tableName
     * @param unknown_type $columnName
     * @param unknown_type $schemaName
     */
    public function tableColumnExists($tableName, $columnName, $schemaName = null)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $tableName
     * @param unknown_type $indexName
     * @param unknown_type $fields
     * @param unknown_type $indexType
     * @param unknown_type $schemaName
     * @return Varien_Db_Adapter_Interface
     */
    public function addIndex($tableName, $indexName, $fields, $indexType = self::INDEX_TYPE_INDEX, $schemaName = null)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $tableName
     * @param unknown_type $keyName
     * @param unknown_type $schemaName
     * @return Varien_Db_Adapter_Interface
     */
    public function dropIndex($tableName, $keyName, $schemaName = null)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $tableName
     * @param unknown_type $schemaName
     */
    public function getIndexList($tableName, $schemaName = null)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $fkName
     * @param unknown_type $tableName
     * @param unknown_type $columnName
     * @param unknown_type $refTableName
     * @param unknown_type $refColumnName
     * @param unknown_type $onDelete
     * @param unknown_type $onUpdate
     * @param unknown_type $purge
     * @param unknown_type $schemaName
     * @param unknown_type $refSchemaName
     * @return Varien_Db_Adapter_Interface
     */
    public function addForeignKey($fkName, $tableName, $columnName, $refTableName, $refColumnName, $onDelete = self::FK_ACTION_CASCADE, $onUpdate = self::FK_ACTION_CASCADE, $purge = false, $schemaName = null, $refSchemaName = null)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $tableName
     * @param unknown_type $fkName
     * @param unknown_type $schemaName
     * @return Varien_Db_Adapter_Interface
     */
    public function dropForeignKey($tableName, $fkName, $schemaName = null)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $tableName
     * @param unknown_type $schemaName
     */
    public function getForeignKeys($tableName, $schemaName = null)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $table
     * @param array $data
     * @param array $fields
     */
    public function insertOnDuplicate($table, array $data, array $fields = array())
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $table
     * @param array $data
     */
    public function insertMultiple($table, array $data)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $table
     * @param array $columns
     * @param array $data
     */
    public function insertArray($table, array $columns, array $data)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $table
     * @param array $bind
     */
    public function insertForce($table, array $bind)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $sql
     * @return Varien_Db_Adapter_Interface
     */
    public function multiQuery($sql)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $date
     * @param unknown_type $includeTime
     */
    public function formatDate($date, $includeTime = true)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @return Varien_Db_Adapter_Interface
     */
    public function startSetup()
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @return Varien_Db_Adapter_Interface
     */
    public function endSetup()
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $adapter
     * @return Varien_Db_Adapter_Interface
     */
    public function setCacheAdapter($adapter)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @return Varien_Db_Adapter_Interface
     */
    public function allowDdlCache()
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @return Varien_Db_Adapter_Interface
     */
    public function disallowDdlCache()
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $tableName
     * @param unknown_type $schemaName
     * @return Varien_Db_Adapter_Interface
     */
    public function resetDdlCache($tableName = null, $schemaName = null)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $tableCacheKey
     * @param unknown_type $ddlType
     * @param unknown_type $data
     * @return Varien_Db_Adapter_Interface
     */
    public function saveDdlCache($tableCacheKey, $ddlType, $data)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $tableCacheKey
     * @param unknown_type $ddlType
     */
    public function loadDdlCache($tableCacheKey, $ddlType)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $fieldName
     * @param unknown_type $condition
     */
    public function prepareSqlCondition($fieldName, $condition)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param array $column
     * @param unknown_type $value
     */
    public function prepareColumnValue(array $column, $value)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $condition
     * @param unknown_type $true
     * @param unknown_type $false
     * @return Zend_Db_Expr
     */
    public function getCheckSql($condition, $true, $false)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $expression
     * @param unknown_type $value
     * @return Zend_Db_Expr
     */
    public function getIfNullSql($expression, $value = 0)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param array $data
     * @param unknown_type $separator
     * @return Zend_Db_Expr
     */
    public function getConcatSql(array $data, $separator = null)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $string
     * @return Zend_Db_Expr
     */
    public function getLengthSql($string)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param array $data
     * @return Zend_Db_Expr
     */
    public function getLeastSql(array $data)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param array $data
     * @return Zend_Db_Expr
     */
    public function getGreatestSql(array $data)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $date
     * @param unknown_type $interval
     * @param unknown_type $unit
     * @return Zend_Db_Expr
     */
    public function getDateAddSql($date, $interval, $unit)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $date
     * @param unknown_type $interval
     * @param unknown_type $unit
     * @return Zend_Db_Expr
     */
    public function getDateSubSql($date, $interval, $unit)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $date
     * @param unknown_type $format
     * @return Zend_Db_Expr
     */
    public function getDateFormatSql($date, $format)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $date
     * @return Zend_Db_Expr
     */
    public function getDatePartSql($date)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $date
     * @param unknown_type $unit
     * @return Zend_Db_Expr
     */
    public function getDateExtractSql($date, $unit)
    {
        // empty code

    }

    /**
     * Returns table name
     *
     * @param string $tableName
     * @return string
     */
    public function getTableName($tableName)
    {
        return $tableName;
    }

    /**
     * Empty method
     *
     * @param unknown_type $tableName
     * @param unknown_type $fields
     * @param unknown_type $indexType
     */
    public function getIndexName($tableName, $fields, $indexType = '')
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $priTableName
     * @param unknown_type $priColumnName
     * @param unknown_type $refTableName
     * @param unknown_type $refColumnName
     */
    public function getForeignKeyName($priTableName, $priColumnName, $refTableName, $refColumnName)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $tableName
     * @param unknown_type $schemaName
     * @return Varien_Db_Adapter_Interface
     */
    public function disableTableKeys($tableName, $schemaName = null)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $tableName
     * @param unknown_type $schemaName
     * @return Varien_Db_Adapter_Interface
     */
    public function enableTableKeys($tableName, $schemaName = null)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param Varien_Db_Select $select
     * @param unknown_type $table
     * @param array $fields
     * @param unknown_type $mode
     */
    public function insertFromSelect(Varien_Db_Select $select, $table, array $fields = array(), $mode = false)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param Varien_Db_Select $select
     * @param unknown_type $table
     */
    public function updateFromSelect(Varien_Db_Select $select, $table)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param Varien_Db_Select $select
     * @param unknown_type $table
     */
    public function deleteFromSelect(Varien_Db_Select $select, $table)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $tableNames
     * @param unknown_type $schemaName
     */
    public function getTablesChecksum($tableNames, $schemaName = null)
    {
        // empty code

    }

    /**
     * Empty method
     *
     */
    public function supportStraightJoin()
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param Varien_Db_Select $select
     * @param unknown_type $field
     * @return Varien_Db_Adapter_Interface
     */
    public function orderRand(Varien_Db_Select $select, $field = null)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $sql
     */
    public function forUpdate($sql)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @param unknown_type $tableName
     * @param unknown_type $schemaName
     */
    public function getPrimaryKeyName($tableName, $schemaName = null)
    {
        // empty code

    }

    /**
     * Empty method
     *
     * @deprecated after 1.5.1.0
     * @return string
     */
    public function getSuggestedZeroDate() {}

    /**
     * Empty method
     *
     * @param mixed $value
     * @return mixed
     */
    public function decodeVarbinary($value) {}
}
