<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rule\Model\Condition\Sql;

use \Magento\Rule\Model\Condition\Combine;
use \Magento\Rule\Model\Condition\AbstractCondition;

/**
 * Class SQL Builder
 *
 * @package Magento\Rule\Model\Condition\Sql
 */
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
     * @var \Magento\Rule\Model\Condition\Sql\ExpressionFactory
     */
    protected $_expressionFactory;

    /**
     * @param ExpressionFactory $expressionFactory
     */
    public function __construct(ExpressionFactory $expressionFactory)
    {
        $this->_expressionFactory = $expressionFactory;
    }

    /**
     * Get tables to join for given conditions combination
     *
     * @param Combine $combine
     * @return array
     * @throws \Magento\Exception
     */
    protected function _getCombineTablesToJoin(Combine $combine)
    {
        $tables = [];
        foreach ($combine->getConditions() as $condition) {
            /** @var $condition AbstractCondition */
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
     * @param Combine $combine
     * @return $this
     */
    protected function _joinTablesToCollection(
        \Magento\Eav\Model\Entity\Collection\AbstractCollection $collection,
        Combine $combine
    ) {
        foreach ($this->_getCombineTablesToJoin($combine) as $alias => $joinTable) {
            /** @var $condition AbstractCondition */
            $collection->getSelect()->joinLeft(
                [$alias => $collection->getResource()->getTable($joinTable['name'])],
                $joinTable['condition']
            );
        }
        return $this;
    }

    /**
     * @param AbstractCondition $condition
     * @param string $value
     * @return string
     * @throws \Magento\Framework\Exception
     */
    protected function _getMappedSqlCondition(AbstractCondition $condition, $value = '')
    {
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
            return $this->_expressionFactory->create(
                ['expression' => $value . $this->_connection->quoteInto($sql, $parsedValue)]
            );
        }
        return '';
    }

    /**
     * @param Combine $combine
     * @param string $value
     * @return string
     */
    protected function _getMappedSqlCombination(Combine $combine, $value = '')
    {
        $out = (!empty($value) ? $value : '');
        $value = ($combine->getValue() ? '' : ' NOT ');
        $getAggregator = $combine->getAggregator();
        $conditions = $combine->getConditions();
        foreach ($conditions as $key => $condition) {
            /** @var $condition AbstractCondition|Combine */
            $con = ($getAggregator == 'any' ? \Zend_Db_Select::SQL_OR : \Zend_Db_Select::SQL_AND);
            $con = (isset($conditions[$key+1]) ? $con : '');
            if ($condition instanceof Combine) {
                $out .= $this->_getMappedSqlCombination($condition, $value);
            } else {
                $out .= $this->_getMappedSqlCondition($condition, $value);
            }
            $out.=  ' ' . $con;
        }
        return $this->_expressionFactory->create(['expression' => $out]);
    }

    /**
     * Attach conditions filter to collection
     *
     * @param \Magento\Eav\Model\Entity\Collection\AbstractCollection $collection
     * @param Combine $combine
     */
    public function attachConditionToCollection(
        \Magento\Eav\Model\Entity\Collection\AbstractCollection $collection,
        Combine $combine
    ) {
        $this->_connection = $collection->getResource()->getReadConnection();
        $this->_joinTablesToCollection($collection, $combine);
        $whereExpression = (string)$this->_getMappedSqlCombination($combine);
        if (!empty($whereExpression)) {
            // Select ::where method adds braces even on empty expression
            $collection->getSelect()->where($whereExpression);
        }
    }
}
