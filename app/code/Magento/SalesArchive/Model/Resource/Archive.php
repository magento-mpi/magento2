<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Archive resource model
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_SalesArchive_Model_Resource_Archive extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Archive entities tables association
     *
     * @var $_tables array
     */
    protected $_tables   = array(
        Magento_SalesArchive_Model_ArchivalList::ORDER
            => array('sales_flat_order_grid', 'magento_sales_order_grid_archive'),
        Magento_SalesArchive_Model_ArchivalList::INVOICE
            => array('sales_flat_invoice_grid', 'magento_sales_invoice_grid_archive'),
        Magento_SalesArchive_Model_ArchivalList::SHIPMENT
            => array('sales_flat_shipment_grid', 'magento_sales_shipment_grid_archive'),
        Magento_SalesArchive_Model_ArchivalList::CREDITMEMO
            => array('sales_flat_creditmemo_grid', 'magento_sales_creditmemo_grid_archive')
    );

    /**
     * Sales archive config
     *
     * @var Magento_SalesArchive_Model_Config
     */
    protected $_salesArchiveConfig;

    /**
     * Sales archival model list
     *
     * @var Magento_SalesArchive_Model_ArchivalList
     */
    protected $_archivalList;

    /**
     * @param Magento_SalesArchive_Model_Config $salesArchiveConfig
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_SalesArchive_Model_ArchivalList $archivalList
     */
    public function __construct(
        Magento_SalesArchive_Model_Config $salesArchiveConfig,
        Magento_Core_Model_Resource $resource,
        Magento_SalesArchive_Model_ArchivalList $archivalList
    ) {
        $this->_salesArchiveConfig = $salesArchiveConfig;
        $this->_archivalList = $archivalList;
        parent::__construct($resource);
    }

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_setResource('magento_salesarchive');
    }

    /**
     * Check archive entity existence
     *
     * @param string $archiveEntity
     * @return boolean
     */
    public function isArchiveEntityExists($archiveEntity)
    {
        return isset($this->_tables[$archiveEntity]);
    }

    /**
     * Get archive entity table
     *
     * @param string $archiveEntity
     * @return string
     */
    public function getArchiveEntityTable($archiveEntity)
    {
        if (!$this->isArchiveEntityExists($archiveEntity)) {
            return false;
        }
        return $this->getTable($this->_tables[$archiveEntity][1]);
    }

    /**
     * Retrieve archive entity source table
     *
     * @param string $archiveEntity
     * @return string
     */
    public function getArchiveEntitySourceTable($archiveEntity)
    {
        if (!$this->isArchiveEntityExists($archiveEntity)) {
            return false;
        }
        return $this->getTable($this->_tables[$archiveEntity][0]);
    }

    /**
     * Retrieve entity ids in archive
     *
     * @param string $archiveEntity
     * @param array|int $ids
     * @return array
     */
    public function getIdsInArchive($archiveEntity, $ids)
    {
        if (!$this->isArchiveEntityExists($archiveEntity) || empty($ids)) {
            return array();
        }

        if (!is_array($ids)) {
            $ids = array($ids);
        }

        $select = $this->_getReadAdapter()->select()
            ->from($this->getArchiveEntityTable($archiveEntity), 'entity_id')
            ->where('entity_id IN(?)', $ids);

        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * Retrieve order ids for archive
     *
     * @param array $orderIds
     * @param boolean $useAge
     * @return array
     */
    public function getOrderIdsForArchive($orderIds = array(), $useAge = false)
    {
        $statuses = $this->_salesArchiveConfig->getArchiveOrderStatuses();
        $archiveAge = ($useAge ? $this->_salesArchiveConfig->getArchiveAge() : 0);

        if (empty($statuses)) {
            return array();
        }

        $select = $this->_getOrderIdsForArchiveSelect($statuses, $archiveAge);
        if (!empty($orderIds)) {
            $select->where('entity_id IN(?)', $orderIds);
        }
        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * Retrieve order ids in archive select
     *
     * @param array $statuses
     * @param int $archiveAge
     * @return Magento_DB_Select
     */
    protected function _getOrderIdsForArchiveSelect($statuses, $archiveAge)
    {
        $adapter = $this->_getReadAdapter();
        $table = $this->getArchiveEntitySourceTable(Magento_SalesArchive_Model_ArchivalList::ORDER);
        $select = $adapter->select()
            ->from($table, 'entity_id')
            ->where('status IN(?)', $statuses);

        if ($archiveAge) { // Check archive age
            $archivePeriodExpr = $adapter->getDateSubSql($adapter->quote($this->formatDate(true)),
                (int) $archiveAge,
                Magento_DB_Adapter_Interface::INTERVAL_DAY
            );
            $select->where($archivePeriodExpr . ' >= updated_at');
        }

        return $select;
    }

    /**
     * Retrieve order ids for archive subselect expression
     *
     * @return Zend_Db_Expr
     */
    public function getOrderIdsForArchiveExpression()
    {
        $statuses = $this->_salesArchiveConfig->getArchiveOrderStatuses();
        $archiveAge = $this->_salesArchiveConfig->getArchiveAge();

        if (empty($statuses)) {
            $statuses = array(0);
        }
        $select = $this->_getOrderIdsForArchiveSelect($statuses, $archiveAge);
        return new Zend_Db_Expr($select);
    }

    /**
     * Move records to from regular grid tables to archive
     *
     * @param string $archiveEntity
     * @param string $conditionField
     * @param array $conditionValue
     * @return Magento_SalesArchive_Model_Resource_Archive
     */
    public function moveToArchive($archiveEntity, $conditionField, $conditionValue)
    {
        if (!$this->isArchiveEntityExists($archiveEntity)) {
            return $this;
        }
        $adapter = $this->_getWriteAdapter();
        $sourceTable = $this->getArchiveEntitySourceTable($archiveEntity);
        $targetTable = $this->getArchiveEntityTable($archiveEntity);

        $insertFields = array_intersect(
            array_keys($adapter->describeTable($targetTable)),
            array_keys($adapter->describeTable($sourceTable))
        );

        $fieldCondition = $adapter->quoteIdentifier($conditionField) . ' IN(?)';
        $select = $adapter->select()
            ->from($sourceTable, $insertFields)
            ->where($fieldCondition, $conditionValue);

        $adapter->query($select->insertFromSelect($targetTable, $insertFields, true));
        return $this;
    }

    /**
     * Remove regords from source grid table
     *
     * @param string $archiveEntity
     * @param string $conditionField
     * @param array $conditionValue
     * @return Magento_SalesArchive_Model_Resource_Archive
     */
    public function removeFromGrid($archiveEntity, $conditionField, $conditionValue)
    {
        if (!$this->isArchiveEntityExists($archiveEntity)) {
            return $this;
        }
        $adapter = $this->_getWriteAdapter();
        $sourceTable = $this->getArchiveEntitySourceTable($archiveEntity);
        $targetTable = $this->getArchiveEntityTable($archiveEntity);
        $sourceResource = $this->_archivalList->getResource($archiveEntity);
        if ($conditionValue instanceof Zend_Db_Expr) {
            $select = $adapter->select();
            // Remove order grid records moved to archive
            $select->from($targetTable, $sourceResource->getIdFieldName());
            $condition = $adapter->quoteInto($sourceResource->getIdFieldName() . ' IN(?)', new Zend_Db_Expr($select));
        } else {
            $fieldCondition = $adapter->quoteIdentifier($conditionField) . ' IN(?)';
            $condition = $adapter->quoteInto($fieldCondition, $conditionValue);
        }

        $adapter->delete($sourceTable, $condition);
        return $this;
    }

    /**
     * Remove records from archive
     *
     * @param string $archiveEntity
     * @param string $conditionField
     * @param array $conditionValue
     * @return Magento_SalesArchive_Model_Resource_Archive
     */
    public function removeFromArchive($archiveEntity, $conditionField = '', $conditionValue = null)
    {
        if (!$this->isArchiveEntityExists($archiveEntity)) {
            return $this;
        }
        $adapter = $this->_getWriteAdapter();
        $sourceTable = $this->getArchiveEntityTable($archiveEntity);
        $targetTable = $this->getArchiveEntitySourceTable($archiveEntity);
        $sourceResource = $this->_archivalList->getResource($archiveEntity);

        $insertFields = array_intersect(
            array_keys($adapter->describeTable($targetTable)),
            array_keys($adapter->describeTable($sourceTable))
        );

        $selectFields = $insertFields;
        $updatedAtIndex = array_search('updated_at', $selectFields);
        if ($updatedAtIndex !== false) {
            unset($selectFields[$updatedAtIndex]);
            $selectFields['updated_at'] = new Zend_Db_Expr($adapter->quoteInto('?', $this->formatDate(true)));
        }

        $select = $adapter->select()
            ->from($sourceTable, $selectFields);

        if (!empty($conditionField)) {
            $select->where($adapter->quoteIdentifier($conditionField) . ' IN(?)', $conditionValue);
        }

        $adapter->query($select->insertFromSelect($targetTable, $insertFields, true));
        if ($conditionValue instanceof Zend_Db_Expr) {
            $select->reset()
                ->from($targetTable, $sourceResource->getIdFieldName()); // Remove order grid records from archive
            $condition = $adapter->quoteInto($sourceResource->getIdFieldName() . ' IN(?)', new Zend_Db_Expr($select));
        } elseif (!empty($conditionField)) {
            $condition = $adapter->quoteInto(
                $adapter->quoteIdentifier($conditionField) . ' IN(?)', $conditionValue
            );
        } else {
            $condition = '';
        }

        $adapter->delete($sourceTable, $condition);
        return $this;
    }

    /**
     * Update grid records
     *
     * @param string $archiveEntity
     * @param array $ids
     * @return Magento_SalesArchive_Model_Resource_Archive
     */
    public function updateGridRecords($archiveEntity, $ids)
    {
        if (!$this->isArchiveEntityExists($archiveEntity) || empty($ids)) {
            return $this;
        }

        /* @var $resource Magento_Sales_Model_Resource_Abstract */
        $resource = $this->_archivalList->getResource($archiveEntity);

        $gridColumns = array_keys($this->_getWriteAdapter()->describeTable(
            $this->getArchiveEntityTable($archiveEntity)
        ));

        $columnsToSelect = array();

        $select = $resource->getUpdateGridRecordsSelect($ids, $columnsToSelect, $gridColumns, true);

        $this->_getWriteAdapter()->query(
            $select->insertFromSelect($this->getArchiveEntityTable($archiveEntity), $columnsToSelect, true)
        );

        return $this;
    }

    /**
     * Find related to order entity ids for checking of new items in archive
     *
     * @param string $archiveEntity
     * @param array $ids
     * @return array
     */
    public function getRelatedIds($archiveEntity, $ids)
    {
        if (empty($archiveEntity) || empty($ids)) {
            return array();
        }

        /** @var $resource Magento_Sales_Model_Resource_Abstract */
        $resource = $this->_archivalList->getResource($archiveEntity);

        $select = $this->_getReadAdapter()->select()
            ->from(array('main_table' => $resource->getMainTable()), 'entity_id')
            ->joinInner( // Filter by archived order
                array('order_archive' => $this->getArchiveEntityTable('order')),
                'main_table.order_id = order_archive.entity_id',
                array()
            )
            ->where('main_table.entity_id IN(?)', $ids);

        return $this->_getReadAdapter()->fetchCol($select);
    }
}
