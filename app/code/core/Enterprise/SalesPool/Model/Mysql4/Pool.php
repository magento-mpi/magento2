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
 * Pool resource model
 *
 */
class Enterprise_SalesPool_Model_Mysql4_Pool extends Mage_Core_Model_Mysql4_Abstract
{
    const POOL_TABLE_PREFIX = 'enterprise_salespool/';
    const POOL_TABLE_SUFIX = '';

    /**
     * Pool tables map to order tables
     *
     * @var array
     */
    protected $_tablesMap = array(
        'sales/order' => 'enterprise_salespool/order',
        'sales/order_item' => 'enterprise_salespool/order_item',
        'sales/order_payment' => 'enterprise_salespool/order_payment',
        'sales/payment_transaction' => 'enterprise_salespool/order_payment_transaction',
        'sales/order_address' => 'enterprise_salespool/order_address',
        'sales/order_status_history' => 'enterprise_salespool/order_status_history',
    );

    const DEFAULT_PRIMARY_COLUMN = 'entity_id';

    protected function _construct()
    {
        $this->_setResource('enterprise_salespool');
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
     * Retrieve last flushed order ids from flat table
     *
     * @return int
     */
    public function getLastFlushedOrderId()
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('sales/order'), array('order_id'=>new Zend_Db_Expr('MAX(entity_id)')));

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Retrive order ids for flush
     *
     * @param int|boolean $lastFlushedOrderId
     * @return array
     */
    public function getOrderIdsForFlush($lastFlushedOrderId = false)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('enterprise_salespool/order'), array('entity_id', 'create_invoice'));

        if ($lastFlushedOrderId !== false) {
            $select->where('entity_id > ?', $lastFlushedOrderId);
        }

        return $this->_getReadAdapter()->fetchPairs($select);
    }

    /**
     * Retrive order ids for flush
     * Filter specified order ids by pool
     *
     * @param array $orderIds
     * @return array
     */
    public function getOrderIdsForFlushByIds($orderIds)
    {
        if (!is_array($orderIds) || empty($orderIds)) {
            return array();
        }

        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('enterprise_salespool/order'), array('entity_id', 'create_invoice'))
            ->where('entity_id IN(?)', $orderIds);

        return $this->_getReadAdapter()->fetchPairs($select);
    }

    /**
     * Update pool table autoincrement
     *
     * @param string $table
     * @param string $primaryColumn
     * @param int|null $newIncrement
     * @return int
     */
    public function updateAutoincrement($entity, $primaryColumn = null, $newIncrement = null)
    {
        if ($primaryColumn === null) {
            $primaryColumn = self::DEFAULT_PRIMARY_COLUMN;
        }

        $table = $this->getTable(self::POOL_TABLE_PREFIX . $entity . self::POOL_TABLE_SUFIX);

        if ($newIncrement === null) {
            $config = $this->_getConfig()->getPoolEntity($entity);
            $sourceTable = $this->getTable($config['source']);
            $select = $this->_getReadAdapter()->select()
                ->from($sourceTable, 'MAX(' . $primaryColumn . ')');
            $newIncrement = $this->_getReadAdapter()->fetchOne($select) + 1;
        } elseif ($newIncrement === false) {
            $newIncrement = '';
        }

        try {
            $this->_getWriteAdapter()->insert($table, array($primaryColumn => $newIncrement));
            if ($newIncrement === '') {
                $newIncrement = $this->_getWriteAdapter()->lastInsertId($table);
            }
            $this->_getWriteAdapter()->delete($table, $primaryColumn . '=' . (int) $newIncrement);
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $newIncrement;
    }

    /**
     * Move records from pool
     *
     * @param string $entity
     * @param mixed $value
     * @param string|null $key
     * @param boolean $valueIsLastId
     * @return Enterprise_SalesPool_Model_Mysql4_Pool
     */
    public function movePoolRecords($entity, $value, $key = null, $valueIsLastId = false)
    {
        $entityConfig = $this->_getConfig()->getPoolEntity($entity);

        if ($entityConfig === false) {
            Mage::throwException(Mage::helper('enterprise_salespool')->__('Unknown pool entity "%s"', $entity));
        }

        if ($key === null && isset($entityConfig['primary'])) {
            $key = $entityConfig['primary'];
        } elseif ($key === null) {
            $key = self::DEFAULT_PRIMARY_COLUMN;
        }

        $poolTable = $this->getTable(self::POOL_TABLE_PREFIX . $entity . self::POOL_TABLE_SUFIX);
        $flatTable = $this->getTable($entityConfig['source']);

        $poolFields = array_keys($this->_getWriteAdapter()->describeTable($poolTable));
        $flatFields = array_keys($this->_getWriteAdapter()->describeTable($flatTable));

        $insertFields = array_intersect($poolFields, $flatFields);

        if (is_array($value)) {
            $condition = $this->_getWriteAdapter()->quoteInto($key . ' IN(?)', $value);
        } elseif ($valueIsLastId) {
            $condition = $this->_getWriteAdapter()->quoteInto($key . ' <= ?', $value);
        } else {
            $condition = $this->_getWriteAdapter()->quoteInto($key . ' = ?', $value);
        }

        /* @var $select Varien_Db_Select */
        $select = $this->_getWriteAdapter()->select()
            ->from($poolTable, $insertFields)
            ->where($condition);

        $this->_getWriteAdapter()->query($select->insertIgnoreFromSelect($flatTable, $insertFields));
        $this->_getWriteAdapter()->delete($poolTable, $condition);
        return $this;
    }

    /**
     * Find mapped table for pool from regular sales table
     *
     * @param string $table
     * @param boolean $revert
     * @return string|boolean
     */
    protected function _findTableMap($table, $revert = false)
    {
        if (strpos($table, '/') !== false) {
            $table = $this->getTable($table);
        }
        foreach ($this->_tablesMap as $mapKey => $mapValue) {
            if (!$revert && $this->getTable($mapKey) === $table) {
                return $mapValue;
            } elseif ($revert && $this->getTable($mapValue) === $table) {
                return $mapKey;
            }
        }

        return false;
    }

    /**
     * Handle update grid rows for entities
     *
     * @param Mage_Sales_Model_Mysql4_Abstract $resource
     * @param array $ids
     * @return array
     */
    public function handleUpdateGridRecords($resource, $ids)
    {
        $mainTable = $resource->getMainTable();

        if ($this->_findTableMap($mainTable, true) !== false) {
            $ids = array();
        }

        return $ids;
    }

    /**
     * Patch object for pool, replace main table for pool
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Enterprise_SalesPool_Model_Mysql4_Pool
     */
    public function applyPatchForObject($object)
    {
        if ($object->getResource() instanceof Mage_Sales_Model_Resource_Order_Abstract) {
            $table = $this->_findTableMap($object->getResource()->getMainTable());
            if ($table) {
                $object->getResource()->setMainTable($table);
            }
        }

        return $this;
    }

    /**
     * Patch collection for pool by replacing main table
     *
     * @param Mage_Sales_Model_Mysql4_Collection_Abstract $collection
     * @return Enterprise_SalesPool_Model_Mysql4_Pool
     */
    public function applyPatchForCollection($collection)
    {
        if ($collection instanceof Mage_Sales_Model_Resource_Collection_Abstract) {
            $table = $this->_findTableMap($collection->getMainTable());
            if ($table) {
                $collection->setMainTable($table);
            }
        }
        return $this;
    }

    /**
     * Patch object for pool, replace main table for pool
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Enterprise_SalesPool_Model_Mysql4_Pool
     */
    public function discardPatchForObject($object)
    {
        if ($object->getResource() instanceof Mage_Sales_Model_Resource_Order_Abstract) {
            $table = $this->_findTableMap($object->getResource()->getMainTable(), true);
            if ($table) {
                $object->getResource()->setMainTable($table);
                $object->setPoolPatched(true);
            }
        }

        return $this;
    }

    /**
     * Mark order for creating of invoice
     *
     * @param Mage_Sales_Model_Order $order
     * @return Enterprise_SalesPool_Model_Mysql4_Pool
     */
    public function markOrderForInvoice(Mage_Sales_Model_Order $order)
    {
        $this->_getWriteAdapter()->update(
            $this->getTable('enterprise_salespool/order'),
            array(
                'create_invoice' => 1,
                'serialized_invoice_data' => $order->getSerializedInvoiceData()
            ),
            'entity_id = ' . (int) $order->getId()
        );
        return $this;
    }

    /**
     * Retrieve serialized invoices data in pool
     *
     * @param array $orderIds
     * @return array
     */
    public function getInvoicesData($orderIds)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('enterprise_salespool/order'), array('entity_id', 'serialized_invoice_data'))
            ->where('entity_id IN(?)', $orderIds);

        return $this->_getReadAdapter()->fetchPairs($select);
    }

    /**
     * Get list of tables referred to base order tables
     * @return array
     */
    public function getReferredTables()
    {
        $tables = array();

        foreach ($this->_tablesMap as $table => $poolTable) {
            $tables[] = $this->getTable($table);
        }
        $allTables = $this->_getWriteAdapter()->fetchCol('SHOW TABLES');
        $referredTables = array();
        foreach ($allTables as $table) {
            $info = $this->_getWriteAdapter()->getForeignKeys($table);
            if (!empty($info)) {
                foreach ($info as $key => $data) {
                    if (in_array($data['REF_TABLE_NAME'], $tables)) {
                        $referredTables[$data['REF_TABLE_NAME']] = $data['TABLE_NAME'];
                    }
                }
            }
        }
        return $referredTables;
    }
}
