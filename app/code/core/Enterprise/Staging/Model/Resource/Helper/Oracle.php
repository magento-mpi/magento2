<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Eav Oracle resource helper model
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Model_Resource_Helper_Oracle extends Mage_Eav_Model_Resource_Helper_Oracle
{
    /**
     * Join information for last staging logs
     *
     * @param  string $table
     * @param  Varien_Db_Select $select
     * @return Varien_Db_Select $select
     */
    public function getLastStagingLogQuery($table, $select)
    {
        $subSelect =  clone $select;
        $subSelect->from($table, array('staging_id', 'log_id', 'action'))
            ->columns('RANK() OVER (PARTITION BY staging_id ORDER BY log_id DESC) as order_log_id');

        $select->from(array('t' => new Zend_Db_Expr('(' . $subSelect . ')')))
            ->where('t.order_log_id = 1');

        return $select;
    }

    /**
     * Modify table properties before Staging Item Data Insert
     *
     * @param string|Varien_Db_Select $sql
     * @param array $tableDesc
     * @return string|Varien_Db_Select $sql
     */
    public function wrapEnableIdentityDataInsert($sql, $tableDesc)
    {
        return $sql;
    }

    /**
     * Modify table properties after Staging Item Data Insert
     *
     * @param array $tableDesc
     * @return Enterprise_Staging_Model_Resource_Helper_Oracle
     */
    public function disableIdentityItemDataInsert($tableDesc)
    {
        return $this;
    }

    /**
     * Retrieve insert from select
     *
     * @param Varien_Db_Select $select
     * @param string $targetTable
     * @param array $fields
     * @return string
     */
    public function getInsertFromSelect($select, $targetTable, $fields)
    {
        $mode = false;
        $compatible = false;
        $indexes    = $this->_getReadAdapter()->getIndexList($targetTable);

        // Obtain unique indexes fields
        foreach ($indexes as $indexData) {
            if ($indexData['INDEX_TYPE'] != Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
                && $indexData['INDEX_TYPE'] != Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY
            ) {
                if ($indexData['INDEX_TYPE'] == Varien_Db_Adapter_Interface::INDEX_TYPE_FULLTEXT) {
                    $compatible = true;
                }
                continue;
            }

            $useUnqCond = true;
            foreach($indexData['COLUMNS_LIST'] as $column) {
                if (!in_array($column, $fields)) {
                    $useUnqCond = false;
                }
            }
            if ($useUnqCond) {
                $mode = Varien_Db_Adapter_Interface::INSERT_ON_DUPLICATE;
            }
        }
        if ($compatible) {
            $sql = $this->_getWriteAdapter()->insertFromSelectCompatible(
                $select,
                $targetTable,
                $fields,
                $mode);
        } else {
            $sql = $this->_getWriteAdapter()->insertFromSelect(
                $select,
                $targetTable,
                $fields,
                $mode);
        }
        return $sql;
    }

    /**
     * Retrieve tables with specified prefix
     *
     * @param string $prefix
     * @return array
     */
    public function getTableNamesByPrefix($prefix)
    {
        $sql    = " SELECT TABLE_NAME FROM all_tab_comments WHERE owner = sys_context('USERENV','CURRENT_SCHEMA') AND "
                . " comments LIKE '% (Prefix:" . $prefix . ")%'";
        return $this->_getReadAdapter()->fetchCol($sql);
    }

    /**
     * Modify table properties before Staging Item Data Insert
     *
     * @param array $tableDesc
     * @return void
     */
    public function beforeIdentityItemDataInsert($tableDesc)
    {
        $this->_getWriteAdapter()->disableTableKeys($tableDesc['table_name']);
    }

    /**
     * Modify table properties after Staging Item Data Insert
     *
     * @param array $tableDesc
     * @return void
     */
    public function afterIdentityItemDataInsert($tableDesc)
    {
        $this->_getWriteAdapter()->enableTableKeys($tableDesc['table_name']);
    }

}
