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
use Magento\Framework\Search\Adapter\Mysql\Filter\Builder\Wildcard;
use Magento\Framework\Search\Request\FilterInterface as RequestFilterInterface;

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
     * @var Wildcard
     */
    private $wildcard;

    /**
     * @param Range $range
     * @param Term $term
     * @param Wildcard $wildcard
     */
    public function __construct(
        Range $range,
        Term $term,
        Wildcard $wildcard
    ) {
        $this->range = $range;
        $this->term = $term;
        $this->wildcard = $wildcard;
    }

    /**
     * {@inheritdoc}
     */
    public function build(RequestFilterInterface $filter)
    {
        switch ($filter->getType()) {
            case RequestFilterInterface::TYPE_BOOL:
                /** @var \Magento\Framework\Search\Request\Filter\Bool $filter */
                $queries = [];
                $must = $this->buildFilters($filter->getMust(), Select::SQL_AND);
                if (!empty($must)) {
                    $queries[] = $must;
                }
                $should = $this->buildFilters($filter->getShould(), Select::SQL_OR);
                if (!empty($should)) {
                    $queries[] = $this->wrapBrackets($should);
                }
                $mustNot = $this->buildFilters($filter->getMustNot(), Select::SQL_AND);
                if (!empty($mustNot)) {
                    $queries[] = '!' . $this->wrapBrackets($mustNot);
                }
                $query = $this->generateQuery($queries, Select::SQL_AND);
                break;
            case RequestFilterInterface::TYPE_TERM:
                /** @var \Magento\Framework\Search\Request\Filter\Term $filter */
                $query = $this->term->buildFilter($filter);
                break;
            case RequestFilterInterface::TYPE_RANGE:
                /** @var \Magento\Framework\Search\Request\Filter\Range $filter */
                $query = $this->range->buildFilter($filter);
                break;
            case RequestFilterInterface::TYPE_WILDCARD:
                /** @var \Magento\Framework\Search\Request\Filter\Wildcard $filter */
                $query = $this->wildcard->buildFilter($filter);
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Unknown filter type \'%s\'', $filter->getType()));
        }
        return $this->wrapBrackets($query);
    }

    /**
     * @param \Magento\Framework\Search\Request\FilterInterface[] $filters
     * @param string $unionOperator
     * @return string
     */
    private function buildFilters(array $filters, $unionOperator)
    {
        $queries = [];
        foreach ($filters as $filter) {
            $queries[] = $this->build($filter);
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
}
