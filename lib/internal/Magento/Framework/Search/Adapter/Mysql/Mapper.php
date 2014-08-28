<?php
/**
 * Mapper class. Maps library request to specific adapter dependent query
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql;

use Magento\Framework\App\Resource\Config;
use Magento\Framework\DB\Select;
use Magento\Framework\Search\Adapter\Mysql\Query\Builder\Match as MatchQueryBuilder;
use Magento\Framework\Search\Request\Query\Bool as BoolQuery;
use Magento\Framework\Search\Request\Query\Filter as FilterQuery;
use Magento\Framework\Search\Request\Query\Match as MatchQuery;
use Magento\Framework\Search\Request\QueryInterface as RequestQueryInterface;
use Magento\Framework\Search\RequestInterface;

class Mapper
{
    /**
     * @var \Magento\Framework\App\Resource
     */
    private $resource;

    /**
     * @var ScoreBuilder
     */
    private $scoreBuilderFactory;

    /**
     * @var \Magento\Framework\Search\Request\Query\Match
     */
    private $matchQueryBuilder;

    /**
     * @param \Magento\Framework\App\Resource $resource
     * @param ScoreBuilderFactory $scoreBuilderFactory
     * @param MatchQueryBuilder $matchQueryBuilder
     */
    public function __construct(
        \Magento\Framework\App\Resource $resource,
        ScoreBuilderFactory $scoreBuilderFactory,
        MatchQueryBuilder $matchQueryBuilder
    ) {
        $this->resource = $resource;
        $this->scoreBuilderFactory = $scoreBuilderFactory;
        $this->matchQueryBuilder = $matchQueryBuilder;
    }

    /**
     * Build adapter dependent query
     *
     * @param RequestInterface $request
     * @return Select
     */
    public function buildQuery(RequestInterface $request)
    {
        $scoreBuilder = $this->scoreBuilderFactory->create();
        $select = $this->processQuery($scoreBuilder, $request->getQuery(), $this->getSelect(), null);
        return $select;
    }

    /**
     * Process query
     *
     * @param ScoreBuilder $scoreBuilder
     * @param RequestQueryInterface $query
     * @param Select $select
     * @param string|null $queryCondition
     * @return Select
     * @throws \InvalidArgumentException
     */
    protected function processQuery(
        ScoreBuilder $scoreBuilder,
        RequestQueryInterface $query,
        Select $select,
        $queryCondition
    ) {
        switch ($query->getType()) {
            case RequestQueryInterface::TYPE_MATCH:
                /** @var MatchQuery $query */
                $scoreBuilder->startQuery();
                $select = $this->matchQueryBuilder->build(
                    $scoreBuilder,
                    $select,
                    $query,
                    $this->isNot($queryCondition)
                );
                $scoreBuilder->endQuery($query->getBoost());
                break;
            case RequestQueryInterface::TYPE_BOOL:
                /** @var BoolQuery $query */
                $select = $this->processBoolQuery($scoreBuilder, $query, $select, $queryCondition);
                break;
            case RequestQueryInterface::TYPE_FILTER:
                /** @var FilterQuery $query */
                $select = $this->processFilterQuery($scoreBuilder, $query, $select, $queryCondition);
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Unknown query type \'%s\'', $query->getType()));
        }
        return $select;
    }

    /**
     * Process bool query
     *
     * @param ScoreBuilder $scoreBuilder
     * @param BoolQuery $query
     * @param Select $select
     * @param string $queryCondition
     * @return Select
     */
    private function processBoolQuery(ScoreBuilder $scoreBuilder, BoolQuery $query, Select $select, $queryCondition)
    {
        $scoreBuilder->startQuery();

        foreach ($query->getMust() as $subQuery) {
            $select = $this->processQuery(
                $scoreBuilder,
                $subQuery,
                $select,
                $this->getFilteredQueryType($queryCondition, BoolQuery::QUERY_CONDITION_MUST)
            );
        }
        foreach ($query->getShould() as $subQuery) {
            $select = $this->processQuery(
                $scoreBuilder,
                $subQuery,
                $select,
                $this->getFilteredQueryType($queryCondition, BoolQuery::QUERY_CONDITION_SHOULD)
            );
        }
        foreach ($query->getMustNot() as $subQuery) {
            $select = $this->processQuery(
                $scoreBuilder,
                $subQuery,
                $select,
                $this->getFilteredQueryType($queryCondition, BoolQuery::QUERY_CONDITION_NOT)
            );
        }

        $scoreBuilder->endQuery($query->getBoost());

        return $select;
    }

    /**
     * Process filter query
     *
     * @param ScoreBuilder $scoreBuilder
     * @param FilterQuery $query
     * @param Select $select
     * @param string $queryCondition
     * @return Select
     */
    private function processFilterQuery(ScoreBuilder $scoreBuilder, FilterQuery $query, Select $select, $queryCondition)
    {
        switch ($query->getReferenceType()) {
            case FilterQuery::REFERENCE_QUERY:
                $scoreBuilder->startQuery();
                $select = $this->processQuery($scoreBuilder, $query->getReference(), $select, $queryCondition);
                $scoreBuilder->endQuery($query->getBoost());
                break;
            case FilterQuery::REFERENCE_FILTER:
                $filterCondition = '(someCondition)'; // add filterBuilder
                if ($queryCondition === BoolQuery::QUERY_CONDITION_NOT) {
                    $filterCondition = '!' . $filterCondition;
                }
                $select->where($filterCondition);
                $scoreBuilder->addCondition(1, $query->getBoost());
                break;
        }
        return $select;
    }

    /**
     * Get empty Select
     *
     * @return Select
     */
    private function getSelect()
    {
        return $this->resource->getConnection(\Magento\Framework\App\Resource::DEFAULT_READ_RESOURCE)->select();
    }

    /**
     * Filter query type
     *
     * @param $queryCondition
     * @param string $defaultQueryCondition
     * @return string
     */
    private function getFilteredQueryType($queryCondition, $defaultQueryCondition = BoolQuery::QUERY_CONDITION_MUST)
    {
        return $queryCondition ? : $defaultQueryCondition;
    }

    /**
     * @param $conditionType
     * @return bool
     */
    private function isNot($conditionType)
    {
        return BoolQuery::QUERY_CONDITION_NOT === $conditionType;
    }
}
