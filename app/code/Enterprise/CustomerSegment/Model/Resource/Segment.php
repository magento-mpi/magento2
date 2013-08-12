<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * CustomerSegment data resource model
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_CustomerSegment_Model_Resource_Segment extends Magento_Rule_Model_Resource_Abstract
{
    /**
     * @var Magento_Customer_Model_Config_Share
     */
    protected $_configShare;

    /**
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Customer_Model_Config_Share $configShare
     */
    public function __construct(
        Magento_Core_Model_Resource $resource,
        Magento_Customer_Model_Config_Share $configShare
    ) {
        parent::__construct($resource);
        $this->_configShare = $configShare;
    }


    /**
     * Store associated with rule entities information map
     *
     * @var array
     */
    protected $_associatedEntitiesMap = array(
        'website' => array(
            'associations_table' => 'enterprise_customersegment_website',
            'rule_id_field'      => 'segment_id',
            'entity_id_field'    => 'website_id'
        ),
        'event' => array(
            'associations_table' => 'enterprise_customersegment_event',
            'rule_id_field'      => 'segment_id',
            'entity_id_field'    => 'event'
        )
    );

    /**
     * Segment websites table name
     *
     * @deprecated after 1.11.2.0
     *
     * @var string
     */
    protected $_websiteTable;

    /**
     * Initialize main table and table id field
     */
    protected function _construct()
    {
        $this->_init('enterprise_customersegment_segment', 'segment_id');
        $this->_websiteTable = $this->getTable('enterprise_customersegment_website');
    }

    /**
     * Add website ids to rule data after load
     *
     * @param Magento_Core_Model_Abstract $object
     *
     * @return Enterprise_CustomerSegment_Model_Resource_Segment
     */
    protected function _afterLoad(Magento_Core_Model_Abstract $object)
    {
        $object->setData('website_ids', (array)$this->getWebsiteIds($object->getId()));

        parent::_afterLoad($object);
        return $this;
    }

    /**
     * Match and save events.
     * Save websites associations.
     *
     * @param Magento_Core_Model_Abstract $object
     *
     * @return Enterprise_CustomerSegment_Model_Resource_Segment
     */
    protected function _afterSave(Magento_Core_Model_Abstract $object)
    {
        $segmentId = $object->getId();

        $this->unbindRuleFromEntity($segmentId, array(), 'event');
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
     * @param   Enterprise_CustomerSegment_Model_Segment $segment
     *
     * @return  Enterprise_CustomerSegment_Model_Resource_Segment
     */
    public function deleteSegmentCustomers($segment)
    {
        $this->_getWriteAdapter()->delete(
            $this->getTable('enterprise_customersegment_customer'),
            array('segment_id=?' => $segment->getId())
        );
        return $this;
    }

    /**
     * Save customer Ids matched by segment SQL select on specific website
     *
     * @param Enterprise_CustomerSegment_Model_Segment $segment
     * @param string $select
     * @return Enterprise_CustomerSegment_Model_Resource_Segment
     * @throws Exception
     */
    public function saveCustomersFromSelect($segment, $select)
    {
        $customerTable = $this->getTable('enterprise_customersegment_customer');
        $adapter = $this->_getWriteAdapter();
        $segmentId = $segment->getId();
        $now = $this->formatDate(time());

        $data = array();
        $count = 0;
        $stmt = $adapter->query($select);
        $adapter->beginTransaction();
        try {
            while ($row = $stmt->fetch()) {
                $data[] = array(
                    'segment_id' => $segmentId,
                    'customer_id' => $row['entity_id'],
                    'website_id' => $row['website_id'],
                    'added_date' => $now,
                    'updated_date' => $now,
                );
                $count++;
                if (($count % 1000) == 0) {
                    $adapter->insertMultiple($customerTable, $data);
                    $data = array();
                }
            }
            if (!empty($data)) {
                $adapter->insertMultiple($customerTable, $data);
            }
        } catch (Exception $e) {
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
     *
     * @return int
     */
    public function getSegmentCustomersQty($segmentId)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getTable('enterprise_customersegment_customer'), array('COUNT(DISTINCT customer_id)'))
            ->where('segment_id = ?', (int)$segmentId);

        return (int)$adapter->fetchOne($select);
    }

    /**
     * Aggregate customer/segments relations by matched segment conditions
     *
     * @param Enterprise_CustomerSegment_Model_Segment $segment
     * @return Enterprise_CustomerSegment_Model_Resource_Segment
     * @throws Exception
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
        } catch (Exception $e) {
            $adapter->rollback();
            throw $e;
        }

        $adapter->commit();

        return $this;
    }

    /**
     * Get select query result
     *
     * @param Magento_DB_Select|string $sql
     * @param array $bindParams array of bind variables
     *
     * @return int
     */
    public function runConditionSql($sql, $bindParams)
    {
        return $this->_getReadAdapter()->fetchOne($sql, $bindParams);
    }

    /**
     * Get empty select object
     *
     * @return Magento_DB_Select
     */
    public function createSelect()
    {
        return $this->_getReadAdapter()->select();
    }

    /**
     * Quote parameters into condition string
     *
     * @param string $string
     * @param string | array $param
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
        return Mage::getResourceHelper('Enterprise_CustomerSegment')->getSqlOperator($operator);
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
                $value = array();
                foreach ($prepareValues as $val) {
                    $value[] = trim($val);
                }
            }
        }

        /*
         * substitute "equal" operator with "is one of" if compared value is not single
         */
        if (count($value) != 1 and in_array($operator, array('==', '!='))) {
            $operator = $operator == '==' ? '()' : '!()';
        }
        $sqlOperator = $this->getSqlOperator($operator);
        $condition = '';

        switch ($operator) {
            case '{}':
            case '!{}':
                if (is_array($value)) {
                    if (!empty($value)) {
                        $condition = array();
                        foreach ($value as $val) {
                            $condition[] = $this->_getReadAdapter()->quoteInto(
                                $field . ' ' . $sqlOperator . ' ?', '%' . $val . '%'
                            );
                        }
                        $condition = implode(' AND ', $condition);
                    }
                } else {
                    $condition = $this->_getReadAdapter()->quoteInto(
                        $field . ' ' . $sqlOperator . ' ?', '%' . $value . '%'
                    );
                }
                break;
            case '()':
            case '!()':
                if (is_array($value) && !empty($value)) {
                    $condition = $this->_getReadAdapter()->quoteInto(
                        $field . ' ' . $sqlOperator . ' (?)', $value
                    );
                }
                break;
            case '[]':
            case '![]':
                if (is_array($value) && !empty($value)) {
                    $conditions = array();
                    foreach ($value as $v) {
                        $conditions[] = $this->_getReadAdapter()->prepareSqlCondition(
                            $field, array('finset' => $this->_getReadAdapter()->quote($v))
                        );
                    }
                    $condition  = sprintf('(%s)=%d', join(' AND ', $conditions), $operator == '[]' ? 1 : 0);
                } else {
                    if ($operator == '[]') {
                        $condition = $this->_getReadAdapter()->prepareSqlCondition(
                            $field, array('finset' => $this->_getReadAdapter()->quote($value))
                        );
                    } else {
                        $condition = 'NOT (' . $this->_getReadAdapter()->prepareSqlCondition(
                            $field, array('finset' => $this->_getReadAdapter()->quote($value))
                        ) . ')';
                    }
                }
                break;
            case 'between':
                $condition = $field . ' ' . sprintf($sqlOperator,
                    $this->_getReadAdapter()->quote($value['start']), $this->_getReadAdapter()->quote($value['end']));
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
     * @deprecated after 1.11.2.0 use $this->bindRuleToEntity() instead
     *
     * @param Magento_Core_Model_Abstract|Enterprise_CustomerSegment_Model_Segment $segment
     *
     * @return Enterprise_CustomerSegment_Model_Resource_Segment
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
     * @param array $segmentIds
     * @return array
     */
    public function getActiveSegmentsByIds($segmentIds)
    {
        $activeSegmentsIds = array();
        if (count($segmentIds)) {
            $adapter = $this->_getWriteAdapter();
            $select = $adapter->select()
                ->from($this->getMainTable(), array('segment_id'))
                ->where('segment_id IN (?)', $segmentIds)
                ->where('is_active = 1');

            $segmentsList = $adapter->fetchAll($select);
            foreach ($segmentsList as $segment) {
                $activeSegmentsIds[] = $segment['segment_id'];
            }
        }
        return $activeSegmentsIds;
    }
}
