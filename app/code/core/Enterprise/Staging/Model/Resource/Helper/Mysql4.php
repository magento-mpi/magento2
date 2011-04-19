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
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
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
     * Returns Ddl Column info from native Db format
     * @param  $field
     * @return array
     */
    public function getDdlInfoByDescription($field)
    {

        $columnName = $field['COLUMN_NAME'];
        $ddlOptions = array();
        $ddlSize = null;
        switch ($field['DATA_TYPE']) {
            case 'bigint':
                $ddlType = Varien_Db_Ddl_Table::TYPE_BIGINT;
                break;
            case 'int':
                $ddlType = Varien_Db_Ddl_Table::TYPE_INTEGER;
                break;
            case 'smallint':
                $ddlType = Varien_Db_Ddl_Table::TYPE_SMALLINT;
                break;
            case 'decimal':
                $ddlType = Varien_Db_Ddl_Table::TYPE_DECIMAL;
                $ddlSize = $field['PRECISION'] . '.' . $field['SCALE'];
            case 'float':
                $ddlType = Varien_Db_Ddl_Table::TYPE_FLOAT;
                break;
            case 'varchar':
            case 'text':
            case 'longtext':
            case 'mediumtext':
                $ddlType = Varien_Db_Ddl_Table::TYPE_TEXT;
                $ddlSize = $field['LENGTH'];
                break;
            case 'varbinary':
            case 'blob':
            case 'longblob':
            case 'mediumblob':
                $ddlType = Varien_Db_Ddl_Table::TYPE_BLOB;
                break;
            case 'datetime':
            case 'timestamp':
                $ddlType = Varien_Db_Ddl_Table::TYPE_TIMESTAMP;
                break;
            case 'date':
                $ddlType = Varien_Db_Ddl_Table::TYPE_DATE;
                break;
            default:Zend_Debug::dump($field);
                echo "PROBLEM:"; //!!!
                exit;
                break;
        }

        if ($field['UNSIGNED']) {
            $ddlOptions['unsigned'] = true;
        }
        if ($field['NULLABLE']) {
            $ddlOptions['nullable'] = true;
        }
        if ($field['IDENTITY']) {
            $ddlOptions['identity'] = true;
        }
        if ($field['PRIMARY']) {
            $ddlOptions['primary'] = true;
        }

        return array($columnName, $ddlType, $ddlSize, $ddlOptions);
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

    /**
     * Add custom option to Table Ddl
     *
     * @param Varien_Db_Ddl_Table $ddlTable
     * @param string $sourceTableName
     * @return void
     */
    public function setCustomTableOptions($ddlTable, $sourceTableName)
    {
        $tableData = $this->_getWriteAdapter()->showTableStatus($sourceTableName);
        $ddlTable->setOption('type', $tableData['Engine']);
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
}
