<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerSegment\Model\Resource;

/**
 * CustomerSegment data resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Segment extends \Magento\Rule\Model\Resource\AbstractResource
{
    /**
     * @var \Magento\Customer\Model\Config\Share
     */
    protected $_configShare;

    /**
     * @var \Magento\CustomerSegment\Model\Resource\Helper
     */
    protected $_resourceHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\CustomerSegment\Model\Resource\Helper $resourceHelper
     * @param \Magento\Customer\Model\Config\Share $configShare
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     */
    public function __construct(
        \Magento\Framework\App\Resource $resource,
        \Magento\CustomerSegment\Model\Resource\Helper $resourceHelper,
        \Magento\Customer\Model\Config\Share $configShare,
        \Magento\Framework\Stdlib\DateTime $dateTime
    ) {
        parent::__construct($resource);
        $this->_resourceHelper = $resourceHelper;
        $this->_configShare = $configShare;
        $this->dateTime = $dateTime;
    }

    /**
     * Store associated with rule entities information map
     *
     * @var array
     */
    protected $_associatedEntitiesMap = [
        'website' => [
            'associations_table' => 'magento_customersegment_website',
            'rule_id_field' => 'segment_id',
            'entity_id_field' => 'website_id',
        ],
        'event' => [
            'associations_table' => 'magento_customersegment_event',
            'rule_id_field' => 'segment_id',
            'entity_id_field' => 'event',
        ],
    ];

    /**
     * Segment websites table name
     *
     * @var string
     * @deprecated after 1.11.2.0
     */
    protected $_websiteTable;

    /**
     * Initialize main table and table id field
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_customersegment_segment', 'segment_id');
        $this->_websiteTable = $this->getTable('magento_customersegment_website');
    }

    /**
     * Add website ids to rule data after load
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        $object->setData('website_ids', (array)$this->getWebsiteIds($object->getId()));

        parent::_afterLoad($object);
        return $this;
    }

    /**
     * Match and save events.
     * Save websites associations.
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $segmentId = $object->getId();

        $this->unbindRuleFromEntity($segmentId, [], 'event');
        if ($object->hasMatchedEvents()) {
            $matchedEvents = $object->getMatchedEvents();
            if (is_array($matchedEvents) && !empty($matchedEvents)) {
                $this->bindRuleToEntity($segmentId, $matchedEvents, 'event');
            }
        }

        if ($object->hasWebsiteIds()) {
            $websiteIds = $object->getWebsiteIds();
            if (!is_array($websiteIds)) {
                $websiteIds = explode(',', (string)$websiteIds);
            }
            $this->bindRuleToEntity($segmentId, $websiteIds, 'website');
        }

        parent::_afterSave($object);
        return $this;
    }

    /**
     * Delete association between customer and segment for specific segment
     *
     * @param \Magento\CustomerSegment\Model\Segment $segment
     * @return $this
     */
    public function deleteSegmentCustomers($segment)
    {
        $this->_getWriteAdapter()->delete(
            $this->getTable('magento_customersegment_customer'),
            ['segment_id=?' => $segment->getId()]
        );
        return $this;
    }

    /**
     * Save customer Ids matched by segment SQL select on specific website
     *
     * @param \Magento\CustomerSegment\Model\Segment $segment
     * @param string $select
     * @return $this
     * @throws \Exception
     */
    public function saveCustomersFromSelect($segment, $select)
    {
        $customerTable = $this->getTable('magento_customersegment_customer');
        $adapter = $this->_getWriteAdapter();
        $segmentId = $segment->getId();
        $now = $this->dateTime->formatDate(time());

        $data = [];
        $count = 0;
        $stmt = $adapter->query($select);
        $adapter->beginTransaction();
        try {
            while ($row = $stmt->fetch()) {
                $data[] = [
                    'segment_id' => $segmentId,
                    'customer_id' => $row['entity_id'],
                    'website_id' => $row['website_id'],
                    'added_date' => $now,
                    'updated_date' => $now,
                ];
                $count++;
                if ($count % 1000 == 0) {
                    $adapter->insertMultiple($customerTable, $data);
                    $data = [];
                }
            }
            if (!empty($data)) {
                $adapter->insertMultiple($customerTable, $data);
            }
        } catch (\Exception $e) {
            $adapter->rollBack();
            throw $e;
        }

        $adapter->commit();

        return $this;
    }

    /**
     * Count customers in specified segment
     *
     * @param int $segmentId
     * @return int
     */
    public function getSegmentCustomersQty($segmentId)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()->from(
            $this->getTable('magento_customersegment_customer'),
            ['COUNT(DISTINCT customer_id)']
        )->where(
            'segment_id = ?',
            (int)$segmentId
        );

        return (int)$adapter->fetchOne($select);
    }

    /**
     * Aggregate customer/segments relations by matched segment conditions
     *
     * @param \Magento\CustomerSegment\Model\Segment $segment
     * @return $this
     * @throws \Exception
     */
    public function aggregateMatchedCustomers($segment)
    {
        $websiteIds = $segment->getWebsiteIds();
        $adapter = $this->_getWriteAdapter();

        $adapter->beginTransaction();
        try {
            $this->deleteSegmentCustomers($segment);
            if (!empty($websiteIds)) {
                if ($this->_configShare->isGlobalScope()) {
                    $query = $segment->getConditions()->getConditionsSql(null, $websiteIds);
                    $this->saveCustomersFromSelect($segment, $query);
                } else {
                    foreach ($websiteIds as $websiteId) {
                        $query = $segment->getConditions()->getConditionsSql(null, $websiteId);
                        $this->saveCustomersFromSelect($segment, $query);
                    }
                }
            }
        } catch (\Exception $e) {
            $adapter->rollback();
            throw $e;
        }

        $adapter->commit();

        return $this;
    }

    /**
     * Get select query result
     *
     * @param \Magento\Framework\DB\Select|string $sql
     * @param array $bindParams array of bind variables
     * @return int
     */
    public function runConditionSql($sql, $bindParams)
    {
        return $this->_getReadAdapter()->fetchOne($sql, $bindParams);
    }

    /**
     * Get empty select object
     *
     * @return \Magento\Framework\DB\Select
     */
    public function createSelect()
    {
        return $this->_getReadAdapter()->select();
    }

    /**
     * Quote parameters into condition string
     *
     * @param string $string
     * @param string|array $param
     * @return string
     */
    public function quoteInto($string, $param)
    {
        return $this->_getReadAdapter()->quoteInto($string, $param);
    }

    /**
     * Get comparison condition for rule condition operator which will be used in SQL query
     * depending of database we using
     *
     * @param string $operator
     * @return string
     */
    public function getSqlOperator($operator)
    {
        return $this->_resourceHelper->getSqlOperator($operator);
    }

    /**
     * Create string for select "where" condition based on field name, comparison operator and field value
     *
     * @param string $field
     * @param string $operator
     * @param mixed $value
     * @return string
     */
    public function createConditionSql($field, $operator, $value)
    {
        if (!is_array($value)) {
            $prepareValues = explode(',', $value);
            if (count($prepareValues) <= 1) {
                $value = $prepareValues[0];
            } else {
                $value = [];
                foreach ($prepareValues as $val) {
                    $value[] = trim($val);
                }
            }
        }

        /*
         * substitute "equal" operator with "is one of" if compared value is not single
         */
        if (count($value) != 1 and in_array($operator, ['==', '!='])) {
            $operator = $operator == '==' ? '()' : '!()';
        }
        $sqlOperator = $this->getSqlOperator($operator);
        $condition = '';

        switch ($operator) {
            case '{}':
            case '!{}':
                if (is_array($value)) {
                    if (!empty($value)) {
                        $condition = [];
                        foreach ($value as $val) {
                            $condition[] = $this->_getReadAdapter()->quoteInto(
                                $field . ' ' . $sqlOperator . ' ?',
                                '%' . $val . '%'
                            );
                        }
                        $condition = implode(' AND ', $condition);
                    }
                } else {
                    $condition = $this->_getReadAdapter()->quoteInto(
                        $field . ' ' . $sqlOperator . ' ?',
                        '%' . $value . '%'
                    );
                }
                break;
            case '()':
            case '!()':
                if (is_array($value) && !empty($value)) {
                    $condition = $this->_getReadAdapter()->quoteInto($field . ' ' . $sqlOperator . ' (?)', $value);
                }
                break;
            case '[]':
            case '![]':
                if (is_array($value) && !empty($value)) {
                    $conditions = [];
                    foreach ($value as $v) {
                        $conditions[] = $this->_getReadAdapter()->prepareSqlCondition(
                            $field,
                            ['finset' => $this->_getReadAdapter()->quote($v)]
                        );
                    }
                    $condition = sprintf('(%s)=%d', join(' AND ', $conditions), $operator == '[]' ? 1 : 0);
                } else {
                    if ($operator == '[]') {
                        $condition = $this->_getReadAdapter()->prepareSqlCondition(
                            $field,
                            ['finset' => $this->_getReadAdapter()->quote($value)]
                        );
                    } else {
                        $condition = 'NOT (' . $this->_getReadAdapter()->prepareSqlCondition(
                            $field,
                            ['finset' => $this->_getReadAdapter()->quote($value)]
                        ) . ')';
                    }
                }
                break;
            case 'between':
                $condition = $field . ' ' . sprintf(
                    $sqlOperator,
                    $this->_getReadAdapter()->quote($value['start']),
                    $this->_getReadAdapter()->quote($value['end'])
                );
                break;
            default:
                $condition = $this->_getReadAdapter()->quoteInto($field . ' ' . $sqlOperator . ' ?', $value);
                break;
        }
        return $condition;
    }

    /**
     * Save all website Ids associated to specified segment
     *
     * @param \Magento\Framework\Model\AbstractModel|\Magento\CustomerSegment\Model\Segment $segment
     * @return $this
     * @deprecated after 1.11.2.0 use $this->bindRuleToEntity() instead
     */
    protected function _saveWebsiteIds($segment)
    {
        if ($segment->hasWebsiteIds()) {
            $websiteIds = $segment->getWebsiteIds();
            if (!is_array($websiteIds)) {
                $websiteIds = explode(',', (string)$websiteIds);
            }
            $this->bindRuleToEntity($segment->getId(), $websiteIds, 'website');
        }

        return $this;
    }

    /**
     * Get Active Segments By Ids
     *
     * @param int[] $segmentIds
     * @return int[]
     */
    public function getActiveSegmentsByIds($segmentIds)
    {
        $activeSegmentsIds = [];
        if (count($segmentIds)) {
            $adapter = $this->_getWriteAdapter();
            $select = $adapter->select()->from(
                $this->getMainTable(),
                ['segment_id']
            )->where(
                'segment_id IN (?)',
                $segmentIds
            )->where(
                'is_active = 1'
            );

            $segmentsList = $adapter->fetchAll($select);
            foreach ($segmentsList as $segment) {
                $activeSegmentsIds[] = $segment['segment_id'];
            }
        }
        return $activeSegmentsIds;
    }
}
