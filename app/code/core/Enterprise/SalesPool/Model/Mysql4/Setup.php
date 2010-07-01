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
 * @package     Enterprise_SalesPool
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Setup pool model
 *
 */
class Enterprise_SalesPool_Model_Mysql4_Setup extends Mage_Core_Model_Resource_Setup
{
    const TYPE_DATETIME = 'datetime';
    const TYPE_VARCHAR = 'varchar';
    const TYPE_TEXT = 'text';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_DECIMAL = 'decimal';
    const TYPE_INT = 'int';

    const DEFAULT_TYPE = self::TYPE_VARCHAR;

    /**
     * Call afterApplyAllUpdates flag
     *
     * @var boolean
     */
    protected $_callAfterApplyAllUpdates = true;

    /**
     * Data types
     *
     * @var array
     */
    protected $_types = array(
        self::TYPE_DATETIME => 'datetime default null',
        self::TYPE_INT      => 'int(11) default null',
        self::TYPE_BOOLEAN  => 'tinyint(1) unsigned not null default \'0\'',
        self::TYPE_VARCHAR  => 'varchar(255) default null',
        self::TYPE_TEXT     => 'text default null',
        self::TYPE_DECIMAL  => 'decimal(12,4) default null'
    );

    /**
     * Sales pool entities table indexes
     * as an associative array where entity_code is key and array_item is indcies definition
     *
     * @example
     *
     * 'entity_code' => array(
     *      'index_name' => array('fields'=>array('field1','field2'), 'type'=>'unique')), // Unique key
     *      'index_name' => array('field1', 'field2'), // Combined index
     *      'index_name' // Single column index
     *      'index_name' => array('type'=>'unique') // Single field unique
     * )
     *
     * @var array
     */
    protected $_poolIndicies = array(
        'order' => array('customer_id'),
        'order_item' => array('order_id'),
        'order_payment' => array('parent_id'),
        'order_payment_transaction' => array(
             'parent_id',
             'order_id',
             'payment_id',
             'order_payment_txn' => array(
                'type'=>'unique',
                'fields' => array('order_id', 'payment_id','txn_id')
             )
        ),
        'order_address' => array('parent_id'),
        'order_status_history' => array('parent_id')
    );

    /**
     * Run each time after applying of all updates,
     * if setup model setted  $_callAfterApplyAllUpdates flag to true
     *
     * @return Enterprise_SalesPool_Model_Mysql4_Setup
     */
    public function afterApplyAllUpdates()
    {
        $this->_syncPoolStructure();
        return $this;
    }

    /**
     * Retrieve pool configuration
     *
     * @return Enterprise_SalesPool_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('enterprise_salespool/config');
    }

    /**
     * Retrieve salespool flag
     *
     * @return Enterprise_SalesPool_Model_Flag
     */
    protected function _getFlag()
    {
        return Mage::getSingleton('enterprise_salespool/flag');
    }

    /**
     * Fast table describe retrieve
     *
     * @param string $table
     * @return array
     */
    protected function _fastDescribe($table)
    {
        return $this->getConnection()->fetchPairs('DESCRIBE ' . $table);
    }

    /**
     * Syncronize pool tables structure with sales flat structure
     *
     * @return void
     */
    protected function _syncPoolStructure()
    {
        foreach ($this->_getConfig()->getPoolEntities() as $entityCode => $entityConfig) {
            if (!isset($entityConfig['source'])) {
                continue;
            }
            $poolTable = Enterprise_SalesPool_Model_Mysql4_Pool::POOL_TABLE_PREFIX . $entityCode . Enterprise_SalesPool_Model_Mysql4_Pool::POOL_TABLE_SUFIX;
            $poolTable = $this->getTable($poolTable);

            $fields = $this->_fastDescribe($this->getTable($entityConfig['source']));

            if ($this->tableExists($poolTable)) {
                $poolFields = $this->_fastDescribe($poolTable);
                foreach ($fields as $field => $definition) {
                    $change = false;
                    if ((isset($poolFields[$field]) && $definition === $poolFields[$field]) || // If column exists
                        (isset($entityConfig['ignore']) && in_array($field, $entityConfig['ignore']))) { // If field added to ignore
                        continue;
                    } elseif (isset($poolFields[$field])) { // If definition changed
                        $change = true;
                    }

                    if ($change) {
                        $this->getConnection()->modifyColumn($poolTable, $field, $definition);
                    } else {
                        $this->getConnection()->addColumn($poolTable, $field, $definition);
                    }
                    $poolFields[$field] = $definition;
                }

                if (isset($entityConfig['fields'])) { // Add custom fields
                    foreach ($entityConfig['fields'] as $code => $type) {
                        if (empty($type)) {
                            $type = self::DEFAULT_TYPE;
                        }
                        if (!isset($this->_types[$type])) {
                            Mage::throwException(
                                Mage::helper('enterprise_salespool')->__('Unknown field type specified for pool')
                            );
                        }
                        $change = false;

                        if (isset($poolFields[$code]) &&
                            strtolower($this->_types[$type]) === strtolower($poolFields[$code])) { // If column exists
                            continue;
                        } elseif (isset($poolFields[$code])) { // If definition changed
                            $change = true;
                        }

                        if ($change) {
                            $this->getConnection()->modifyColumn($poolTable, $code, $this->_types[$type]);
                        } else {
                            $this->getConnection()->addColumn($poolTable, $code, $this->_types[$type]);
                        }
                        $poolFields[$field] = $definition;
                    }
                }
            } else {
                $primary = Enterprise_SalesPool_Model_Mysql4_Pool::DEFAULT_PRIMARY_COLUMN;

                if (isset($entityConfig['primary'])) {
                    $primary = $entityConfig['primary'];
                }

                $sql = 'CREATE TABLE ' . $this->getConnection()->quoteIdentifier($poolTable) . ' ( ';
                foreach ($fields as $field => $definition) {
                    if ($field === $primary) {
                        $definition .= ' AUTO_INCREMENT';
                    }

                    if ((isset($entityConfig['ignore']) && in_array($field, $entityConfig['ignore']))) {
                        continue;
                    }
                    $sql .= ' ' . $this->getConnection()->quoteIdentifier($field) . ' ' . $definition . ',';
                }
                if (isset($entityConfig['fields'])) {
                    foreach ($entityConfig['fields'] as $code=>$type) {
                        if (empty($type)) {
                            $type = self::DEFAULT_TYPE;
                        }

                        if (!isset($this->_types[$type])) {
                            Mage::throwException(
                                Mage::helper('enterprise_salespool')->__('Unknown field type specified for pool')
                            );
                        }

                        $sql .= ' ' . $this->getConnection()->quoteIdentifier($code) . ' ' . $this->_types[$type] . ',';
                    }
                }


                $sql .= ' PRIMARY KEY(' .  $this->getConnection()->quoteIdentifier($primary) . ')) ENGINE=InnoDB DEFAULT CHARSET=utf8';

                $this->getConnection()->query($sql);
                $poolFields = $this->_fastDescribe($poolTable);
            }

            if (isset($this->_poolIndicies[$entityCode])) {
                // Synchronize pool indicies
                $indecies = $this->getConnection()->getKeyList($poolTable);

                foreach ($this->_poolIndicies[$entityCode] as $indexName => $indexInfo) {
                    if (!is_string($indexName) && is_string($indexInfo)) {
                        // If it is just string array item, it means that indexInfo is indexName and fields
                        $indexName = $indexInfo;
                        $fields = array($indexName);
                        $type = 'index';
                    } elseif (is_array($indexInfo) && !isset($indexInfo['fields']) && isset($indexInfo['type'])) {
                        // If we have only type definition
                        // it means that index name match it index field
                        $fields = array($indexName);
                        $type = $indexInfo['type'];
                    } elseif (is_array($indexInfo) && isset($indexInfo['fields'])) {
                        // If we have full defition of columns
                        $fields = (is_array($indexInfo['fields']) ? $indexInfo['fields'] : array($indexInfo['fields']));
                        $type = (isset($indexInfo['type']) ? $indexInfo['type'] : 'index');
                    } elseif (is_array($indexInfo)) {
                        // If array dont have 'field' and 'type' keys it means that fields is array items
                        $fields = $indexInfo;
                    } else {
                        // invalid index definition
                        continue;
                    }

                    $type = strtolower($type);
                    if (!in_array($type, array('index', 'unique'))) {
                        $type = 'index';
                    }

                    // Check fields existance in pool table
                    $fields = array_intersect(array_keys($poolFields), $fields);

                    if (empty($fields)) {
                        continue;
                    }

                    $indexName = strtoupper(($type == 'unique'? 'unq_':'idx_') . $indexName);

                    if (!isset($indecies[$indexName]) || count(array_diff($indecies[$indexName], $fields)) > 0) {
                        $this->getConnection()->addKey($poolTable, $indexName, $fields, $type);
                    }
                }
            }
        }
    }
}
