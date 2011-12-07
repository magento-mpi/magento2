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
 * Enter description here ...
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Enterprise_Staging_Model_Resource_Adapter_Abstract extends Mage_Core_Model_Resource_Db_Abstract
    implements Enterprise_Staging_Model_Resource_Adapter_Interface
{
    /**
     * Replace direction for mapping table name
     */
    const REPLACE_DIRECTION_TO      = true;

    /**
     * Replace direction for mapping table name
     */
    const REPLACE_DIRECTION_FROM    = false;

    /**
     * Staging instance
     *
     * @var object Enterprise_Staging_Model_Staging
     */
    protected $_staging;

    /**
     * Event instance
     *
     * @var object Enterprise_Staging_Model_Staging_Event
     */
    protected $_event;

    /**
     * Staging type config data
     *
     * @var mixed
     */
    protected $_config;

    /**
     * Flat type table list
     *
     * @var mixed
     */
    protected $_flatTables     = array(
        'catalog/category_flat' => array(
            'helper' => 'Mage_Catalog_Helper_Category_Flat',
            'resource_model' => 'Mage_Catalog_Model_Resource_Category_Flat'
        ),
        'catalog/product_flat'  => array(
            'helper' => 'Mage_Catalog_Helper_Product_Flat',
            'resource_model' => 'Mage_Catalog_Model_Resource_Product_Flat'
        )
    );

    /**
     * EAV type Table models
     *
     * @var mixed
     */
    protected $_eavModels      = array(
        'catalog_product_entity'           => 'catalog',
        'catalog_category_entity'          => 'catalog',
        'sales_flat_order'               => 'sales',
        'sales_order_entity'        => 'sales',
        'customer_entity'           => 'customer',
        'customer_address_entity'   => 'customer',
    );

    /**
     * Table names replaces map
     *
     * @var mixed
     */
    protected $_tableNameMap = array(
        'catalog'   => 'ctl',
        'category'  => 'ctg',
        'entity'    => 'ntt',
        'product'   => 'prd',
        'salesrule' => 'slsr',
        'downloadable' => 'dnl',
        'index'     => 'idx',
        'price'     => 'prc'
    );

    /**
     * EAV table entities
     *
     * @var modex
     */
    protected $_eavTableTypes  = array('int', 'decimal', 'varchar', 'text', 'datetime');

    /**
     * Enter description here ...
     *
     */
    protected function _construct()
    {
        $this->_setResource('enterprise_staging');
    }

    /**
     * Staging content check
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param Enterprise_Staging_Model_Staging_Event $event
     * @return Enterprise_Staging_Model_Resource_Adapter_Abstract
     */
    public function checkfrontendRun(Enterprise_Staging_Model_Staging $staging, $event = null)
    {
        $this->setStaging($staging);
        $this->setEvent($event);
        return $this;
    }

    /**
     * Create item method
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param unknown_type $event
     * @return Enterprise_Staging_Model_Resource_Adapter_Abstract
     */
    public function createRun(Enterprise_Staging_Model_Staging $staging, $event = null)
    {
        $this->setStaging($staging);
        $this->setEvent($event);
        return $this;
    }

    /**
     * Update item method
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param unknown_type $event
     * @return Enterprise_Staging_Model_Resource_Adapter_Abstract
     */
    public function updateRun(Enterprise_Staging_Model_Staging $staging, $event = null)
    {
        $this->setStaging($staging);
        $this->setEvent($event);
        return $this;
    }

    /**
     * Create Staging content backup
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param Enterprise_Staging_Model_Staging_Event $event
     * @return Enterprise_Staging_Model_Resource_Adapter_Abstract
     */
    public function backupRun(Enterprise_Staging_Model_Staging $staging, $event = null)
    {
        $this->setStaging($staging);
        $this->setEvent($event);
        return $this;
    }

    /**
     * Make staging content merge
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param Enterprise_Staging_Model_Staging_Event $event
     * @return Enterprise_Staging_Model_Resource_Adapter_Abstract
     */
    public function mergeRun(Enterprise_Staging_Model_Staging $staging, $event = null)
    {
        $this->setStaging($staging);
        $this->setEvent($event);
        return $this;
    }

    /**
     * Make staging content rollback
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param Enterprise_Staging_Model_Staging_Event $event
     * @return Enterprise_Staging_Model_Resource_Adapter_Abstract
     */
    public function rollbackRun(Enterprise_Staging_Model_Staging $staging, $event = null)
    {
        $this->setStaging($staging);
        $this->setEvent($event);
        return $this;
    }

    /**
     * Get all backup tables
     *
     * @param  Enterprise_Staging_Model_Staging $staging
     * @param  Enterprise_Staging_Model_Staging_Event|null $event
     * @return Enterprise_Staging_Model_Resource_Adapter_Abstract
     */
    public function getBackupTablesRun(Enterprise_Staging_Model_Staging $staging, $event = null)
    {
        $this->setStaging($staging);
        $this->setEvent($event);
        return $this;
    }

    /**
     * Specify event instance
     *
     * @param unknown_type $event
     * @return Enterprise_Staging_Model_Resource_Adapter_Abstract
     */
    public function setEvent($event)
    {
        $this->_event = $event;
        return $this;
    }

    /**
     * Retrieve event object
     *
     * @return Enterprise_Staging_Model_Staging
     */
    public function getEvent()
    {
        return $this->_event;
    }

    /**
     * Specify staging instance
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @return Enterprise_Staging_Model_Resource_Adapter_Abstract
     */
    public function setStaging(Enterprise_Staging_Model_Staging $staging)
    {
        $this->_staging = $staging;
        return $this;
    }

    /**
     * Retrieve staging object
     *
     * @return Enterprise_Staging_Model_Staging
     */
    public function getStaging()
    {
        return $this->_staging;
    }

    /**
     * Specify item xml config
     *
     * @param Varien_Simplexml_Config $config
     * @return Enterprise_Staging_Model_Resource_Adapter_Abstract
     */
    public function setConfig($config)
    {
        $this->_config = $config;
        return $this;
    }

    /**
     * Retrieve item xml config
     *
     * @return Varien_Simplexml_Config
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Enter description here ...
     *
     * @param unknown_type $fields
     * @return unknown
     */
    protected function allowToProceedInWebsiteScope($fields)
    {
        if (in_array('website_id', $fields) || in_array('website_ids', $fields) || in_array('scope_id', $fields)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Enter description here ...
     *
     * @param unknown_type $fields
     * @return unknown
     */
    protected function allowToProceedInStoreScope($fields)
    {
        if (in_array('store_id', $fields) || in_array('store_ids', $fields) || in_array('scope_id', $fields)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Create table
     *
     * @param string $tableName
     * @param string $srcTableName
     * @param bool $isFlat Ignored, left for compatibility
     * @return Enterprise_Staging_Model_Resource_Adapter_Abstract
     */
    public function createTable($tableName, $srcTableName, $isFlat = false)
    {
        $srcTableDesc = $this->getTableProperties($srcTableName);
        if (!$srcTableDesc) {
            return $this;
        }
        $srcTableDesc['table_name'] = $this->getTable($tableName);
        $srcTableDesc['src_table_name'] = $this->getTable($srcTableName);
        $newTable = $this->_getCreateDdl($srcTableDesc);

        try {
            $this->_getWriteAdapter()->createTable($newTable);
        } catch (Exception $e) {
            $message = Mage::helper('Enterprise_Staging_Helper_Data')->__('An exception occurred while performing an SQL query: %s. ', $e->getMessage());
            throw new Enterprise_Staging_Exception($message);
        }
        return $this;
    }

    /**
     * Clone Table data
     *
     * @param string $sourceTableName
     * @param string $targetTableName
     * @return Enterprise_Staging_Model_Resource_Adapter_Abstract
     */
    public function cloneTable($sourceTableName, $targetTableName)
    {
        // validate tables
        $sourceDesc = $this->getTableProperties($sourceTableName);
        $targetDesc = $this->getTableProperties($targetTableName);

        $diff = array_diff_key($sourceDesc['fields'], $targetDesc['fields']);
        if ($diff) {
            $message = Mage::helper('Enterprise_Staging_Helper_Data')->__('Staging Table "%s" and Master Tables "%s" has different fields', $targetTableName, $sourceTableName);
            throw new Enterprise_Staging_Exception($message);
        }

        /* @var $select Varien_Db_Select */
        $fields = array_keys($sourceDesc['fields']);
        $select = $this->_getWriteAdapter()->select()
            ->from(array('s' => $sourceTableName), $fields);
        $sql = $select->insertFromSelect($targetTableName, $fields);

        try {
            $this->_getWriteAdapter()->query($sql);
        }
        catch (Zend_Db_Exception $e) {
            $message = Mage::helper('Enterprise_Staging_Helper_Data')->__('An exception occurred while performing an SQL query: %s. Query: %s', $e->getMessage(), $sql);
            throw new Enterprise_Staging_Exception($message);
        }

        return $this;
    }

    /**
     * Check table for existing and create it if not
     *
     * @param string $tableName
     * @param string $srcTableName
     * @param string $prefix
     * @return Enterprise_Staging_Model_Resource_Adapter_Abstract
     */
    protected function _checkCreateTable($tableName, $srcTableName, $prefix)
    {
        $tableDesc = $this->getTableProperties($tableName);
        if (!$tableDesc) {
            $srcTableDesc = $this->getTableProperties($srcTableName);
            if ($srcTableDesc) {
                $srcTableDesc['table_name'] = $tableName;
                $srcTableDesc['src_table_name'] = $srcTableName;
                $newTable = $this->_getCreateDdl($srcTableDesc);
                $newTable->setComment($newTable->getComment() . ' (Prefix:' . $prefix . ')');
                $this->_getWriteAdapter()->createTable($newTable);
            }
        }
        return $this;
    }

    /**
     * Get create table Ddl
     *
     * @param array $tableDescription
     * @return Varien_Db_Ddl_Table
     */
    protected function _getCreateDdl($tableDescription)
    {
        $adapter = $this->_getReadAdapter();

        $newTableName = $adapter->getTableName($tableDescription['table_name']);
        $srcTableName = $adapter->getTableName($tableDescription['src_table_name']);
        $newTable = $adapter->createTableByDdl($srcTableName, $newTableName);

        foreach ($newTable->getColumns(false) as $column) {
            $column['COMMENT'] = 'Staging ' . $column['COMMENT'];
            $newTable->setColumn($column);
        }
        $newTable->setComment($tableDescription['comment']);
        return $newTable;
    }

    /**
     * Retrieve table properties as array
     * fields, keys, constraints, engine, charset, create
     *
     * @param string $entityName
     * @param bool $strongRestrict
     * @return array
     */
    public function getTableProperties($entityName, $strongRestrict = false)
    {
        $table = $this->getTable($entityName);
        $adapter = $this->_getWriteAdapter();

        if (!$this->tableExists($table)) {
            if ($strongRestrict) {
                throw new Enterprise_Staging_Exception(Mage::helper('Enterprise_Staging_Helper_Data')->__('Staging Table %s does not exist', $table));
            }
            return false;
        }

        $tableProp = array(
            'table_name'    => $table,
            'fields'        => $adapter->describeTable($table),
            'indexes'       => $adapter->getIndexList($table),
            'foreign_keys'  => $adapter->getForeignKeys($table),
            'comment'       => 'Staging table',
        );

        return $tableProp;
    }

    /**
     * Check exists table
     *
     * @param string $table
     * @return bool
     */
    public function tableExists($table)
    {
        return $this->_getWriteAdapter()->isTableExists($table);
    }

    /**
     * Prepare simple select by given parameters
     *
     * @param mixed $entityName
     * @param mixed $fields
     * @param string | array $where
     * @return string
     */
    protected function _getSimpleSelect($entityName, $fields, $where = null)
    {
        $select = $this->_getWriteAdapter()->select()->from($this->getTable($entityName), $fields);
        if (isset($where)) {
            if (is_array($where)) {
                foreach ($where as $cond => $value) {
                    if (is_int($cond)) {
                        $select->where($value);
                    } else {
                        $select->where($cond, $value);
                    }
                }
            } else {
                $select->where($where);
            }
        }

        return $select;
    }

    /**
     * Delete rows by Unique fields
     *
     * @param string $type
     * @param string $scope
     * @param string $srcTable
     * @param string $targetTable
     * @param mixed $masterIds
     * @param mixed $slaveIds
     * @param mixed $keys
     * @param string $additionalWhereCondition
     * @return string
     */
    protected function _deleteDataByKeys($type, $scope, $srcTable, $targetTable, $masterIds, $slaveIds, $keys,
        $additionalWhereCondition = null)
    {
        $adapter = $this->_getWriteAdapter();
        $select = $adapter->select();
        $select->from(array('TGT' => $targetTable), array());
        if (is_array($masterIds)) {
            $masterWhere = ' IN (' . implode(', ', $masterIds). ') ';
        } else {
            $masterWhere = ' = ' . $masterIds;
        }
        if (is_array($slaveIds)) {
            $slaveWhere = ' IN (' . implode(', ', $slaveIds). ') ';
        } else {
            $slaveWhere = ' = ' . $slaveIds;
        }

        foreach ($keys as $keyData) {
            if ($keyData['type'] == $type) {
                $_websiteFieldNameSql = array();
                foreach ($keyData['fields'] as $field) {

                    if ($field == 'website_id' || $field == 'store_id') {
                        $_websiteFieldNameSql[] = " T1.`{$field}` $slaveWhere
                            AND T2.`{$field}` $masterWhere ";
                    } elseif ($field == 'scope_id') {
                        $_websiteFieldNameSql[] = " T1.`scope` = '{$scope}' AND T1.`{$field}` $slaveWhere
                            AND T2.`{$field}` $masterWhere ";
                    } else { //website_ids is update data as rule, so it must be in backup.
                        $_websiteFieldNameSql[] = "T1.`$field` = T2.`$field`";
                    }
                }

                $sql = "DELETE T1.* FROM `{$targetTable}` as T1, `{$srcTable}` as T2 WHERE "
                    . implode(' AND ', $_websiteFieldNameSql);

                $select->join(
                    array('SRCT' => $srcTable),
                    implode(' AND ', $_websiteFieldNameSql),
                    array()
                );

                if (!empty($additionalWhereCondition)) {
                    $additionalWhereCondition = str_replace(array($srcTable, $targetTable), array('T2', 'T1'),
                        $additionalWhereCondition);
                    $sql .= ' AND ' . $additionalWhereCondition;
                }
                $adapter->deleteFromSelect($select, $targetTable);

                return $sql;
            }
        }

        return '';
    }

    /**
     * Retrieve table name for the entity
     *
     * @param string $entityName
     * @return string
     */
    public function getTable($entityName)
    {
        if (is_array($entityName) || strpos($entityName, '/') !== false) {
            $table = parent::getTable($entityName);
        } else {
            $table = $entityName;
        }
        return $table;
    }

    /**
     * Return Staging table name with all prefixes
     *
     * @param string $table
     * @param string $internalPrefix
     * @return string
     */
    public function getStagingTableName($table, $internalPrefix = '')
    {
        if ($internalPrefix) {
            $tablePrefix = Mage::getSingleton('Enterprise_Staging_Model_Staging_Config')
                ->getTablePrefix($this->getStaging(), $internalPrefix);
            $table = $tablePrefix . substr($table, strlen(Mage::getConfig()->getTablePrefix()));
            return $this->_getWriteAdapter()->getTableName($table);
        }
        return $table;
    }

    /**
     * Maping table name
     *
     * @param string $tableName
     * @param bool $direction
     * @return string
     */
    protected function _mapTableName($tableName, $direction = self::REPLACE_DIRECTION_TO)
    {
        foreach ($this->_tableNameMap as $from => $to) {
            if ($direction == self::REPLACE_DIRECTION_TO) {
                $tableName = str_replace($from, $to, $tableName);
            } else {
                $tableName = str_replace($to, $from, $tableName);
            }
        }

        return $tableName;
    }

}
