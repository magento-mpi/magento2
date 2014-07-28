<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract Rule product condition data model
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rule\Model\Condition\Sql;

class Builder
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $_connection;

    /**
     * @var array
     */
    protected $_conditionOperatorMap = [
        '=='    => ':field = ?',
        '!='    => ':field <> ?',
        '>='    => ':field => ?',
        '>'     => ':field > ?',
        '<='    => ':field <= ?',
        '<'     => ':field < ?',
        '{}'    => ':field IN (?)',
        '!{}'   => ':field NOT IN (?)',
        '()'    => ':field IN (?)',
        '!()'   => ':field NOT IN (?)',
    ];

    /**
     * Get tables to join for given conditions combination
     *
     * @param \Magento\Rule\Model\Condition\Combine $combine
     * @return array
     * @throws \Magento\Exception
     */
    protected function _getCombineTablesToJoin(\Magento\Rule\Model\Condition\Combine $combine)
    {
        $tables = [];
        foreach ($combine->getConditions() as $condition) {
            /** @var $condition \Magento\Rule\Model\Condition\AbstractCondition */
            foreach ($condition->getTablesToJoin() as $alias => $table) {
                if (!isset($tables[$alias])) {
                    $tables[$alias] = $table;
                }
            }
        }
        return $tables;
    }

    /**
     * Join tables from conditions combination to collection
     *
     * @param \Magento\Eav\Model\Entity\Collection\AbstractCollection $collection
     * @param \Magento\Rule\Model\Condition\Combine $combine
     * @return $this
     */
    protected function _joinTablesToCollection(
        \Magento\Eav\Model\Entity\Collection\AbstractCollection $collection,
        \Magento\Rule\Model\Condition\Combine $combine
    ) {
        foreach ($this->_getCombineTablesToJoin($combine) as $alias => $joinTable) {
            /** @var $condition \Magento\Rule\Model\Condition\AbstractCondition */
            $collection->getSelect()->joinLeft(
                [$alias => $collection->getResource()->getTable($joinTable['name'])],
                $joinTable['condition']
            );
        }
        return $this;
    }

    /**
     * @param \Magento\Rule\Model\Condition\AbstractCondition $condition
     * @param string $value
     * @return string
     * @throws \Magento\Framework\Exception
     */
    protected function _getMappedSqlCondition(\Magento\Rule\Model\Condition\AbstractCondition $condition, $value = '')
    {
        $out = ' ';
        $argument = $condition->getMappedSqlField();
        if ($argument) {
            $conditionOperator = $condition->getOperator();

            $parsedValue = $condition->getValueParsed();
            if (!isset($this->_conditionOperatorMap[$conditionOperator])) {
                throw new \Magento\Framework\Exception('Unknown condition operator');
            }
            $sql = str_replace(
                ':field',
                $this->_connection->quoteIdentifier($argument),
                $this->_conditionOperatorMap[$conditionOperator]
            );
            $out .= $value . $this->_connection->quoteInto($sql, $parsedValue) . ') ';
        }
        return $out;
    }

    /**
     * @param \Magento\Rule\Model\Condition\Combine $combine
     * @param string $value
     * @return string
     */
    protected function _getMappedSqlCombination(\Magento\Rule\Model\Condition\Combine $combine, $value = '')
    {
        $out = (!empty($value) ? $value : '(');
        $value = ($combine->getValue() ? '(' : ' NOT (');
        $getAggregator = $combine->getAggregator();
        $conditions = $combine->getConditions();
        foreach ($conditions as $key => $condition) {
            /** @var $condition \Magento\Rule\Model\Condition\AbstractCondition */
            $con = ($getAggregator == 'any' ? \Zend_Db_Select::SQL_OR : \Zend_Db_Select::SQL_AND);
            $con = (isset($conditions[$key+1]) ? $con : '');
            if ($condition instanceof \Magento\Rule\Model\Condition\Combine) {
                $out .= $this->_getMappedSqlCombination($condition, $value);
            } else {
                $out .= $this->_getMappedSqlCondition($condition, $value);
            }
            $out.=  ' ' . $con;
        }
        $out .= ')';
        return $out;
    }

    /**
     * Attach conditions filter to collection
     *
     * @param \Magento\Eav\Model\Entity\Collection\AbstractCollection $collection
     * @param \Magento\Rule\Model\Condition\Combine $combine
     */
    public function attachConditionToCollection(
        \Magento\Eav\Model\Entity\Collection\AbstractCollection $collection,
        \Magento\Rule\Model\Condition\Combine $combine
    ) {
        $this->_connection = $collection->getResource()->getReadConnection();
        $this->_joinTablesToCollection($collection, $combine);
        $collection->getSelect()->where($this->_getMappedSqlCombination($combine));
    }
}
