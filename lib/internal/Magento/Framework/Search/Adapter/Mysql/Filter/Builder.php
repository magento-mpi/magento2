<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql\Filter;

use Magento\Framework\DB\Select;
use Magento\Framework\Search\Adapter\Mysql\Filter\Builder\Range;
use Magento\Framework\Search\Adapter\Mysql\Filter\Builder\Term;
use Magento\Framework\Search\Request\FilterInterface as RequestFilterInterface;
use Magento\Framework\Search\Request\Query\Bool;

class Builder implements BuilderInterface
{
    /**
     * @var Range
     */
    private $range;
    /**
     * @var Term
     */
    private $term;

    /**
     * @param Range $range
     * @param Term $term
     */
    public function __construct(
        Range $range,
        Term $term
    ) {
        $this->range = $range;
        $this->term = $term;
    }

    /**
     * {@inheritdoc}
     */
    public function build(RequestFilterInterface $filter, $conditionType)
    {
        return $this->processFilter($filter, $this->isNegation($conditionType));
    }

    /**
     * @param RequestFilterInterface $filter
     * @param bool $isNegation
     * @return string
     */
    private function processFilter(RequestFilterInterface $filter, $isNegation)
    {
        switch ($filter->getType()) {
            case RequestFilterInterface::TYPE_BOOL:
                $query = $this->processBoolFilter($filter, $isNegation);
                break;
            case RequestFilterInterface::TYPE_TERM:
                $query = $this->processTermFilter($filter, $isNegation);
                break;
            case RequestFilterInterface::TYPE_RANGE:
                $query = $this->processRangeFilter($filter, $isNegation);
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Unknown filter type \'%s\'', $filter->getType()));
        }
        return $this->wrapBrackets($query);
    }

    /**
     * @param $conditionType
     * @return bool
     */
    private function isNegation($conditionType)
    {
        return Bool::QUERY_CONDITION_NOT === $conditionType;
    }

    /**
     * @param RequestFilterInterface|\Magento\Framework\Search\Request\Filter\Bool $filter
     * @param $isNegation
     * @return string
     */
    private function processBoolFilter(RequestFilterInterface $filter, $isNegation)
    {
        $queries = [];

        $must = $this->buildFilters($filter->getMust(), Select::SQL_AND, $isNegation);
        if (!empty($must)) {
            $queries[] = $must;
        }

        $should = $this->buildFilters($filter->getShould(), Select::SQL_OR, $isNegation);
        if (!empty($should)) {
            $queries[] = $this->wrapBrackets($should);
        }

        $mustNot = $this->buildFilters(
            $filter->getMustNot(),
            Select::SQL_AND,
            !$isNegation
        );
        if (!empty($mustNot)) {
            $queries[] = $this->wrapBrackets($mustNot);
        }
        return $this->generateQuery($queries, Select::SQL_AND);
    }

    /**
     * @param RequestFilterInterface|\Magento\Framework\Search\Request\Filter\Term $filter
     * @param $isNegation
     * @return Select|string
     */
    private function processTermFilter(RequestFilterInterface $filter, $isNegation)
    {
        return $this->term->buildFilter($filter, $isNegation);
    }

    /**
     * @param RequestFilterInterface|\Magento\Framework\Search\Request\Filter\Range $filter
     * @param $isNegation
     * @return Select|string
     */
    private function processRangeFilter(RequestFilterInterface $filter, $isNegation)
    {
        return $this->range->buildFilter($filter, $isNegation);
    }

    /**
     * @param string $query
     * @return string
     */
    private function wrapBrackets($query)
    {
        return empty($query)
            ? $query
            : '(' . $query . ')';
    }

    /**
     * @param \Magento\Framework\Search\Request\FilterInterface[] $filters
     * @param string $unionOperator
     * @param bool $isNegation
     * @return string
     */
    private function buildFilters(array $filters, $unionOperator, $isNegation)
    {
        $queries = [];
        foreach ($filters as $filter) {
            $queries[] = $this->processFilter($filter, $isNegation);
        }
        return $this->generateQuery($queries, $unionOperator);
    }

    /**
     * @param string[] $queries
     * @param string $unionOperator
     * @return string
     */
    private function generateQuery(array $queries, $unionOperator)
    {
        $query = implode(
            ' ' . $unionOperator . ' ',
            $queries
        );
        return $query;
    }
}
