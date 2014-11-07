<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Model\Resource;

/**
 * Archive resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Archive extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Archive entities tables association
     *
     * @var $_tables array
     */
    protected $_tables = array(
        \Magento\SalesArchive\Model\ArchivalList::ORDER => array(
            'sales_order_grid',
            'magento_sales_order_grid_archive'
        ),
        \Magento\SalesArchive\Model\ArchivalList::INVOICE => array(
            'sales_invoice_grid',
            'magento_sales_invoice_grid_archive'
        ),
        \Magento\SalesArchive\Model\ArchivalList::SHIPMENT => array(
            'sales_shipment_grid',
            'magento_sales_shipment_grid_archive'
        ),
        \Magento\SalesArchive\Model\ArchivalList::CREDITMEMO => array(
            'sales_creditmemo_grid',
            'magento_sales_creditmemo_grid_archive'
        )
    );

    /**
     * Sales archive config
     *
     * @var \Magento\SalesArchive\Model\Config
     */
    protected $_salesArchiveConfig;

    /**
     * Sales archival model list
     *
     * @var \Magento\SalesArchive\Model\ArchivalList
     */
    protected $_archivalList;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\SalesArchive\Model\Config $salesArchiveConfig
     * @param \Magento\SalesArchive\Model\ArchivalList $archivalList
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     */
    public function __construct(
        \Magento\Framework\App\Resource $resource,
        \Magento\SalesArchive\Model\Config $salesArchiveConfig,
        \Magento\SalesArchive\Model\ArchivalList $archivalList,
        \Magento\Framework\Stdlib\DateTime $dateTime
    ) {
        $this->_salesArchiveConfig = $salesArchiveConfig;
        $this->_archivalList = $archivalList;
        $this->dateTime = $dateTime;
        parent::__construct($resource);
    }

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_setResource('magento_salesarchive');
    }

    /**
     * Check archive entity existence
     *
     * @param string $archiveEntity
     * @return bool
     */
    public function isArchiveEntityExists($archiveEntity)
    {
        return isset($this->_tables[$archiveEntity]);
    }

    /**
     * Get archive entity table
     *
     * @param string $archiveEntity
     * @return false|string
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
     * @return false|string
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

        $select = $this->_getReadAdapter()->select()->from(
            $this->getArchiveEntityTable($archiveEntity),
            'entity_id'
        )->where(
            'entity_id IN(?)',
            $ids
        );

        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * Retrieve order ids for archive
     *
     * @param array $orderIds
     * @param bool $useAge
     * @return array
     */
    public function getOrderIdsForArchive($orderIds = array(), $useAge = false)
    {
        $statuses = $this->_salesArchiveConfig->getArchiveOrderStatuses();
        $archiveAge = $useAge ? $this->_salesArchiveConfig->getArchiveAge() : 0;

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
     * @return \Magento\Framework\DB\Select
     */
    protected function _getOrderIdsForArchiveSelect($statuses, $archiveAge)
    {
        $adapter = $this->_getReadAdapter();
        $table = $this->getArchiveEntitySourceTable(\Magento\SalesArchive\Model\ArchivalList::ORDER);
        $select = $adapter->select()->from($table, 'entity_id')->where('status IN(?)', $statuses);

        if ($archiveAge) {
            // Check archive age
            $archivePeriodExpr = $adapter->getDateSubSql(
                $adapter->quote($this->dateTime->formatDate(true)),
                (int)$archiveAge,
                \Magento\Framework\DB\Adapter\AdapterInterface::INTERVAL_DAY
            );
            $select->where($archivePeriodExpr . ' >= updated_at');
        }

        return $select;
    }

    /**
     * Retrieve order ids for archive subselect expression
     *
     * @return \Zend_Db_Expr
     */
    public function getOrderIdsForArchiveExpression()
    {
        $statuses = $this->_salesArchiveConfig->getArchiveOrderStatuses();
        $archiveAge = $this->_salesArchiveConfig->getArchiveAge();

        if (empty($statuses)) {
            $statuses = array(0);
        }
        $select = $this->_getOrderIdsForArchiveSelect($statuses, $archiveAge);
        return new \Zend_Db_Expr($select);
    }

    /**
     * Move records to from regular grid tables to archive
     *
     * @param string $archiveEntity
     * @param string $conditionField
     * @param array $conditionValue
     * @return $this
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
        $select = $adapter->select()->from($sourceTable, $insertFields)->where($fieldCondition, $conditionValue);

        $adapter->query($select->insertFromSelect($targetTable, $insertFields, true));
        return $this;
    }

    /**
     * Remove regords from source grid table
     *
     * @param string $archiveEntity
     * @param string $conditionField
     * @param array $conditionValue
     * @return $this
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
        if ($conditionValue instanceof \Zend_Db_Expr) {
            $select = $adapter->select();
            // Remove order grid records moved to archive
            $select->from($targetTable, $sourceResource->getIdFieldName());
            $condition = $adapter->quoteInto($sourceResource->getIdFieldName() . ' IN(?)', new \Zend_Db_Expr($select));
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
     * @param null $conditionValue
     * @return $this
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
            $selectFields['updated_at'] = new \Zend_Db_Expr(
                $adapter->quoteInto('?', $this->dateTime->formatDate(true))
            );
        }

        $select = $adapter->select()->from($sourceTable, $selectFields);

        if (!empty($conditionField)) {
            $select->where($adapter->quoteIdentifier($conditionField) . ' IN(?)', $conditionValue);
        }

        $adapter->query($select->insertFromSelect($targetTable, $insertFields, true));
        if ($conditionValue instanceof \Zend_Db_Expr) {
            $select->reset()->from($targetTable, $sourceResource->getIdFieldName());
            // Remove order grid records from archive
            $condition = $adapter->quoteInto($sourceResource->getIdFieldName() . ' IN(?)', new \Zend_Db_Expr($select));
        } elseif (!empty($conditionField)) {
            $condition = $adapter->quoteInto($adapter->quoteIdentifier($conditionField) . ' IN(?)', $conditionValue);
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
     * @return $this
     */
    public function updateGridRecords($archiveEntity, $ids)
    {
        if (!$this->isArchiveEntityExists($archiveEntity) || empty($ids)) {
            return $this;
        }

        /* @var $resource \Magento\Sales\Model\Resource\AbstractResource */
        $resource = $this->_archivalList->getResource($archiveEntity);

        $gridColumns = array_keys(
            $this->_getWriteAdapter()->describeTable($this->getArchiveEntityTable($archiveEntity))
        );

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

        /** @var $resource \Magento\Sales\Model\Resource\AbstractResource */
        $resource = $this->_archivalList->getResource($archiveEntity);

        $select = $this->_getReadAdapter()->select()->from(
            array('main_table' => $resource->getMainTable()),
            'entity_id'
        )->joinInner(
            // Filter by archived order
            array('order_archive' => $this->getArchiveEntityTable('order')),
            'main_table.order_id = order_archive.entity_id',
            array()
        )->where(
            'main_table.entity_id IN(?)',
            $ids
        );

        return $this->_getReadAdapter()->fetchCol($select);
    }
}
