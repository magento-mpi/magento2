<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql\Filter\Builder;

use Magento\Framework\App\Resource;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Search\Request\Filter\Range as RangeFilterRequest;
use Magento\Framework\Search\Request\FilterInterface as RequestFilterInterface;

class Range implements FilterInterface
{
    const CONDITION_PART_GREATER_THAN = '>=';
    const CONDITION_PART_LOWER_THAN = '<';

    /**
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * @param \Magento\Framework\App\Resource $resource
     */
    public function __construct(\Magento\Framework\App\Resource $resource)
    {
        $this->adapter = $resource->getConnection(Resource::DEFAULT_READ_RESOURCE);
    }

    /**
     * {@inheritdoc}
     */
    public function buildFilter(
        RequestFilterInterface $filter,
        $isNegation
    ) {
        /** @var RangeFilterRequest $filter */
        return $this->generateCondition(
            $filter,
            $isNegation
        );
    }

    /**
     * @param RequestFilterInterface|RangeFilterRequest $filter
     * @param bool $isNegation
     * @return string
     */
    private function generateCondition(RequestFilterInterface $filter, $isNegation)
    {
        $leftPart = $this->generateConditionLeftPart($filter, $isNegation);
        $rightPart = $this->generateConditionRightPart($filter, $isNegation);
        $unionOperator = $this->generateConditionUnionOperator($leftPart, $rightPart, $isNegation);

        return $leftPart . $unionOperator . $rightPart;
    }

    /**
     * @param RequestFilterInterface|RangeFilterRequest $filter
     * @param bool $isNegation
     * @return string
     */
    private function generateConditionLeftPart(RequestFilterInterface $filter, $isNegation)
    {
        return $this->generateConditionPart(
            $filter->getField(),
            $filter->getFrom(),
            ($isNegation ? self::CONDITION_PART_LOWER_THAN : self::CONDITION_PART_GREATER_THAN)
        );
    }

    /**
     * @param RequestFilterInterface|RangeFilterRequest $filter
     * @param bool $isNegation
     * @return string
     */
    private function generateConditionRightPart(RequestFilterInterface $filter, $isNegation)
    {
        return $this->generateConditionPart(
            $filter->getField(),
            $filter->getTo(),
            ($isNegation ? self::CONDITION_PART_GREATER_THAN : self::CONDITION_PART_LOWER_THAN)
        );
    }

    /**
     * Generate condition part
     *
     * @param string $field
     * @param string|integer|float|null $value
     * @param string $operator
     * @return string
     */
    private function generateConditionPart($field, $value, $operator)
    {
        $condition = '';
        if (!is_null($value)) {
            $condition = sprintf(
                '%s %s %s',
                $field,
                $operator,
                $this->quote($value)
            );
        }
        return $condition;
    }

    /**
     * Quote sql value
     *
     * @param mixed $value The value to quote.
     * @param mixed $type OPTIONAL the SQL datatype name, or constant, or null.
     * @return mixed An SQL-safe quoted value (or string of separated values).
     */
    private function quote($value, $type = null)
    {
        return $this->adapter->quote($value, $type);
    }

    /**
     * @param string $leftPart
     * @param string $rightPart
     * @param bool $isNegation
     * @return string
     */
    private function generateConditionUnionOperator($leftPart, $rightPart, $isNegation)
    {
        $condition = '';
        if (!empty($leftPart) and !empty($rightPart)) {
            $condition = ' ' . ($isNegation ? \Zend_Db_Select::SQL_OR : \Zend_Db_Select::SQL_AND) . ' ';
        }
        return $condition;
    }
}
