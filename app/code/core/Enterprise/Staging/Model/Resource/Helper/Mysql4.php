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
 * Eav Mysql4 resource helper model
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Model_Resource_Helper_Mysql4 extends Mage_Eav_Model_Resource_Helper_Mysql4
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
            ->order('log_id DESC');

        $select->from(array('t' => new Zend_Db_Expr('(' . $subSelect . ')')))
            ->group('staging_id');

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
     * @return Enterprise_Staging_Model_Resource_Helper_Mysql4
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
        return $this->_getWriteAdapter()->insertFromSelect(
                $select,
                $targetTable,
                $fields,
                Varien_Db_Adapter_Interface::INSERT_ON_DUPLICATE);

    }

    /**
     * Retrieve tables with specified prefix
     *
     * @param string $prefix
     * @return array
     */
    public function getTableNamesByPrefix($prefix)
    {
        $sql    = "SHOW TABLES LIKE '" . str_replace('_', '\_', $prefix) . "%'";
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
