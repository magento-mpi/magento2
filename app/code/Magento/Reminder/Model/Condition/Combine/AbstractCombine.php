<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Reminder\Model\Condition\Combine;

use Magento\Framework\DB\Select;

/**
 * Abstract class for combine rule condition
 */
abstract class AbstractCombine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * Rule Resource
     *
     * @var \Magento\Reminder\Model\Resource\Rule
     */
    protected $_ruleResource;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Reminder\Model\Resource\Rule $ruleResource
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Reminder\Model\Resource\Rule $ruleResource,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_ruleResource = $ruleResource;
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
     * @return \Magento\Reminder\Model\Resource\Rule
     */
    public function getResource()
    {
        return $this->_ruleResource;
    }

    /**
     * Get filter by customer condition for rule matching sql
     *
     * @param null|int|\Zend_Db_Expr $customer
     * @param string $fieldName
     * @return string
     */
    protected function _createCustomerFilter($customer, $fieldName)
    {
        return "{$fieldName} = root.entity_id";
    }

    /**
     * Build query for matching customer to rule condition
     *
     * @param null|int|\Zend_Db_Expr $customer
     * @param int|\Zend_Db_Expr $website
     * @return Select
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
     * Get SQL select for matching customer to rule condition
     *
     * @param null|int|\Zend_Db_Expr $customer
     * @param int|\Zend_Db_Expr $website
     * @return Select
     */
    public function getConditionsSql($customer, $website)
    {
        /**
         * Build base SQL
         */
        $select = $this->_prepareConditionsSql($customer, $website);
        $required = $this->_getRequiredValidation();
        $whereFunction = $this->getAggregator() == 'all' ? 'where' : 'orWhere';
        $operator = $required ? '=' : '<>';
        //$operator       = '=';

        $gotConditions = false;

        /**
         * Add children sub-selects conditions
         */
        foreach ($this->getConditions() as $condition) {
            if ($sql = $condition->getConditionsSql($customer, $website)) {
                $criteriaSql = "(" . $select->getAdapter()->getIfNullSql("(" . $sql . ")", 0) . " {$operator} 1)";
                $select->{$whereFunction}($criteriaSql);
                $gotConditions = true;
            }
        }

        /**
         * Process combine sub-filters. Sub-filters are part of base select which can be affected by children.
         */
        $subfilterMap = $this->_getSubfilterMap();
        if ($subfilterMap) {
            foreach ($this->getConditions() as $condition) {
                $subfilterType = $condition->getSubfilterType();
                if (isset($subfilterMap[$subfilterType])) {
                    $subfilter = $condition->getSubfilterSql($subfilterMap[$subfilterType], $required, $website);
                    if ($subfilter) {
                        $select->{$whereFunction}($subfilter);
                        $gotConditions = true;
                    }
                }
            }
        }

        if (!$gotConditions) {
            $select->where('1=1');
        }

        return $select;
    }

    /**
     * Get information about sub-filters map.
     *
     * Map contain children condition type and associated column name from itself select.
     * Example: array('my_subtype'=>'my_table.my_column')
     * In practice - date range can be as sub-filter for different types of condition combines.
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
     * @param int|Zend_Db_Expr $website
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
            'store.website_id=?',
            $website
        );
        return $this;
    }
}
