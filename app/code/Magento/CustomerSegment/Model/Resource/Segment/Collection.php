<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerSegment\Model\Resource\Segment;

/**
 * Enterprise CustomerSegment Model Resource Segment Collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Rule\Model\Resource\Rule\Collection\AbstractCollection
{
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
     * Fields map for correlation names & real selected fields
     *
     * @var array
     */
    protected $_map = ['fields' => ['website_id' => 'website.website_id']];

    /**
     * Store flag which determines if customer count data was added
     *
     * @var bool
     * @deprecated after 1.11.2.0 - use $this->getFlag('is_customer_count_added') instead
     */
    protected $_customerCountAdded = false;

    /**
     * Set resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\CustomerSegment\Model\Segment', 'Magento\CustomerSegment\Model\Resource\Segment');
    }

    /**
     * Limit segments collection by event name
     *
     * @param string $eventName
     * @return $this
     */
    public function addEventFilter($eventName)
    {
        $entityInfo = $this->_getAssociatedEntityInfo('event');
        if (!$this->getFlag('is_event_table_joined')) {
            $this->setFlag('is_event_table_joined', true);
            $this->getSelect()->joinInner(
                ['evt' => $this->getTable($entityInfo['associations_table'])],
                'main_table.' . $entityInfo['rule_id_field'] . ' = evt.' . $entityInfo['rule_id_field'],
                []
            );
        }
        $this->getSelect()->where('evt.' . $entityInfo['entity_id_field'] . ' = ?', $eventName);
        return $this;
    }

    /**
     * Provide support for customer count filter
     *
     * @param string $field
     * @param int|string|array|null $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'customer_count') {
            return $this->addCustomerCountFilter($condition);
        } elseif ($field == $this->getResource()->getIdFieldName()) {
            $field = 'main_table.' . $field;
        }

        parent::addFieldToFilter($field, $condition);
        return $this;
    }

    /**
     * Retrieve collection items as option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('segment_id', 'name');
    }

    /**
     * Get SQL for get record count.
     * Reset left join, group and having parts
     *
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        if ($this->getFlag('is_customer_count_added')) {
            $countSelect->reset(\Zend_Db_Select::GROUP);
            $countSelect->reset(\Zend_Db_Select::HAVING);
            $countSelect->resetJoinLeft();
        }
        return $countSelect;
    }

    /**
     * Aggregate customer count by each segment
     *
     * @return $this
     */
    public function addCustomerCountToSelect()
    {
        if ($this->getFlag('is_customer_count_added')) {
            return $this;
        }
        $this->setFlag('is_customer_count_added', true);
        $this->_customerCountAdded = true;

        $this->getSelect()->joinLeft(
            ['customer_count_table' => $this->getTable('magento_customersegment_customer')],
            'customer_count_table.segment_id = main_table.segment_id',
            ['customer_count' => new \Zend_Db_Expr('COUNT(customer_count_table.customer_id)')]
        )->group(
            'main_table.segment_id'
        );
        return $this;
    }

    /**
     * Add customer count filter
     *
     * @param int $customerCount
     * @return $this
     */
    public function addCustomerCountFilter($customerCount)
    {
        $this->addCustomerCountToSelect();
        $this->getSelect()->having('customer_count = ?', $customerCount);
        return $this;
    }

    /**
     * Retrieve all ids for collection
     *
     * @return array
     */
    public function getAllIds()
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(\Zend_Db_Select::ORDER);
        $idsSelect->reset(\Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(\Zend_Db_Select::LIMIT_OFFSET);
        $select = $this->getConnection()->select()->from(
            ['t' => new \Zend_Db_Expr(sprintf('(%s)', $idsSelect))],
            ['t.' . $this->getResource()->getIdFieldName()]
        );
        return $this->getConnection()->fetchCol($select, $this->_bindParams);
    }
}
