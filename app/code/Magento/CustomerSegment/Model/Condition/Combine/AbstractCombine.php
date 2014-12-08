<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Model\Condition\Combine;

use Magento\Customer\Model\Customer;

abstract class AbstractCombine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * @var \Magento\CustomerSegment\Model\Resource\Segment
     */
    protected $_resourceSegment;

    /**
     * @var \Magento\CustomerSegment\Model\ConditionFactory
     */
    protected $_conditionFactory;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\CustomerSegment\Model\ConditionFactory $conditionFactory
     * @param \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\CustomerSegment\Model\ConditionFactory $conditionFactory,
        \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment,
        array $data = []
    ) {
        $this->_resourceSegment = $resourceSegment;
        parent::__construct($context, $data);
        $this->_conditionFactory = $conditionFactory;
    }

    /**
     * Flag of using condition combine (for conditions of Product_Attribute)
     *
     * @var bool
     */
    protected $_combineProductCondition = false;

    /**
     * Get array of event names where segment with such conditions combine can be matched
     *
     * @return array
     */
    public function getMatchedEvents()
    {
        return [];
    }

    /**
     * Customize default operator input by type mapper for some types
     *
     * @return array
     */
    public function getDefaultOperatorInputByType()
    {
        if (null === $this->_defaultOperatorInputByType) {
            parent::getDefaultOperatorInputByType();
            $this->_defaultOperatorInputByType['numeric'] = ['==', '!=', '>=', '>', '<=', '<'];
            $this->_defaultOperatorInputByType['string'] = ['==', '!=', '{}', '!{}'];
        }
        return $this->_defaultOperatorInputByType;
    }

    /**
     * Add operator when loading array
     *
     * @param array $arr
     * @param string $key
     * @return $this
     */
    public function loadArray($arr, $key = 'conditions')
    {
        if (isset($arr['operator'])) {
            $this->setOperator($arr['operator']);
        }

        if (isset($arr['attribute'])) {
            $this->setAttribute($arr['attribute']);
        }

        return parent::loadArray($arr, $key);
    }

    /**
     * Get condition combine resource model
     *
     * @return \Magento\CustomerSegment\Model\Resource\Segment
     */
    public function getResource()
    {
        return $this->_resourceSegment;
    }

    /**
     * Get filter by customer condition for segment matching sql
     *
     * @param Customer|\Zend_Db_Expr $customer
     * @param string $fieldName
     * @return string
     */
    protected function _createCustomerFilter($customer, $fieldName)
    {
        if ($customer) {
            $customerFilter = "{$fieldName} = :customer_id";
        } else {
            $customerFilter = "{$fieldName} = root.entity_id";
        }
        return $customerFilter;
    }

    /**
     * Build query for matching customer to segment condition
     *
     * @param Customer|\Zend_Db_Expr $customer
     * @param int|\Zend_Db_Expr $website
     * @return \Magento\Framework\DB\Select
     */
    protected function _prepareConditionsSql($customer, $website)
    {
        $select = $this->getResource()->createSelect();
        $table = $this->getResource()->getTable('customer_entity');
        $select->from($table, [new \Zend_Db_Expr(1)]);
        $select->where($this->_createCustomerFilter($customer, 'entity_id'));
        return $select;
    }

    /**
     * Check if condition is required. It affect condition select result comparison type (= || <>)
     *
     * @return bool
     */
    protected function _getRequiredValidation()
    {
        return $this->getValue() == 1;
    }

    /**
     * Get information if condition is required
     *
     * @return bool
     */
    public function getIsRequired()
    {
        return $this->_getRequiredValidation();
    }

    /**
     * Get information if it's used as a child of History or List condition
     *
     * @return bool
     */
    public function getCombineProductCondition()
    {
        return $this->_combineProductCondition;
    }

    /**
     * Get SQL select for matching customer to segment condition
     *
     * @param Customer|\Zend_Db_Select|\Zend_Db_Expr $customer
     * @param int|\Zend_Db_Expr $website
     * @return \Magento\Framework\DB\Select
     */
    public function getConditionsSql($customer, $website)
    {
        /**
         * Build base SQL
         */
        $select = $this->_prepareConditionsSql($customer, $website);
        $required = $this->_getRequiredValidation();
        $aggregator = $this->getAggregator() == 'all' ? ' AND ' : ' OR ';
        $operator = $required ? '=' : '<>';
        $conditions = [];

        /**
         * Add children subselects conditions
         */
        $adapter = $this->getResource()->getReadConnection();
        foreach ($this->getConditions() as $condition) {
            if ($sql = $condition->getConditionsSql($customer, $website)) {
                $isnull = $adapter->getCheckSql($sql, 1, 0);
                if ($condition->getCombineProductCondition()) {
                    $sqlOperator = $condition->getIsRequired() ? '=' : '<>';
                } else {
                    $sqlOperator = $operator;
                }
                $conditions[] = "({$isnull} {$sqlOperator} 1)";
            }
        }

        /**
         * Process combine subfilters. Subfilters are part of base select which cah be affected by children.
         */
        $subfilterMap = $this->_getSubfilterMap();
        if ($subfilterMap) {
            foreach ($this->getConditions() as $condition) {
                $subfilterType = $condition->getSubfilterType();
                if (isset($subfilterMap[$subfilterType])) {
                    $condition->setCombineProductCondition($this->_combineProductCondition);
                    $subfilter = $condition->getSubfilterSql($subfilterMap[$subfilterType], $required, $website);
                    if ($subfilter) {
                        $conditions[] = $subfilter;
                    }
                }
            }
        }

        if (!empty($conditions)) {
            $select->where(implode($aggregator, $conditions));
        }

        return $select;
    }

    /**
     * Get infromation about subfilters map. Map contain children condition type and associated
     * column name from itself select.
     * Example: array('my_subtype'=>'my_table.my_column')
     * In practice - date range can be as subfilter for different types of condition combines.
     * Logic of this filter apply is same - but column names different
     *
     * @return array
     */
    protected function _getSubfilterMap()
    {
        return [];
    }

    /**
     * Limit select by website with joining to store table
     *
     * @param \Zend_Db_Select $select
     * @param int|\Zend_Db_Expr $website
     * @param string $storeIdField
     * @return $this
     */
    protected function _limitByStoreWebsite(\Zend_Db_Select $select, $website, $storeIdField)
    {
        $storeTable = $this->getResource()->getTable('store');
        $select->join(
            ['store' => $storeTable],
            $storeIdField . '=store.store_id',
            []
        )->where(
            'store.website_id IN (?)',
            $website
        );
        return $this;
    }
}
