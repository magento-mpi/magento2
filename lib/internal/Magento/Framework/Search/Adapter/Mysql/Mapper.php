<?php
/**
 * Mapper class. Maps library request to specific adapter dependent query
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql;

use Magento\Framework\App\Resource\Config;
use Magento\Framework\DB\Select;
use Magento\Framework\Search\Request\Query\Bool as BoolQuery;
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
     * @var ScoreManager
     */
    private $scoreManager;

    /**
     * @param \Magento\Framework\App\Resource $resource
     * @param ScoreManager $scoreManager
     */
    public function __construct(
        \Magento\Framework\App\Resource $resource,
        ScoreManager $scoreManager
    ) {
        $this->resource = $resource;
        $this->scoreManager = $scoreManager;
    }

    /**
     * Build adapter dependent query
     *
     * @param RequestInterface $request
     * @return Select
     */
    public function buildQuery(RequestInterface $request)
    {
        $select = $this->processQuery($request->getQuery(), $this->getSelect());
        return $select;
    }

    /**
     * Process query
     *
     * @param RequestQueryInterface $query
     * @param Select $select
     * @param null|string $queryCondition
     * @return Select
     */
    protected function processQuery(RequestQueryInterface $query, Select $select, $queryCondition = null)
    {
        switch ($query->getType()) {
            case RequestQueryInterface::TYPE_MATCH:
                /** @var MatchQuery $query */
//                $matchQueryBuilder->buildQuery(
//                    $select,
//                    $query,
//                    $this->getFilteredQueryType($queryCondition, BoolQuery::QUERY_CONDITION_MUST)
//                );
                break;
            case RequestQueryInterface::TYPE_BOOL:
                /** @var BoolQuery $query */
                $this->processBoolQuery($query, $select, $queryCondition);
                break;
            case RequestQueryInterface::TYPE_FILTER:
                /** @var \Magento\Framework\Search\Request\Query\Filter $query */
//                $filterQueryBuilder->buildQuery(
//                    $select,
//                    $query,
//                    $this->getFilteredQueryType($queryCondition, BoolQuery::QUERY_CONDITION_MUST)
//                );
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Unknown query type \'%s\'', $query->getType()));
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
     * Process bool query
     *
     * @param BoolQuery $query
     * @param Select $select
     * @param string $queryCondition
     * @return void
     */
    private function processBoolQuery(BoolQuery $query, Select $select, $queryCondition)
    {
        foreach ($query->getMust() as $subQuery) {
            $this->processQuery(
                $subQuery,
                $select,
                $this->getFilteredQueryType($queryCondition, BoolQuery::QUERY_CONDITION_MUST)
            );
        }
        foreach ($query->getShould() as $subQuery) {
            $this->processQuery(
                $subQuery,
                $select,
                $this->getFilteredQueryType($queryCondition, BoolQuery::QUERY_CONDITION_SHOULD)
            );
        }
        foreach ($query->getMustNot() as $subQuery) {
            $this->processQuery(
                $subQuery,
                $select,
                $this->getFilteredQueryType($queryCondition, BoolQuery::QUERY_CONDITION_NOT)
            );
        }
    }

    /**
     * Filter query type
     *
     * @param string|null $queryCondition
     * @param string $defaultQueryType
     * @return string
     */
    private function getFilteredQueryType($queryCondition, $defaultQueryCondition = BoolQuery::QUERY_CONDITION_MUST)
    {
        return $queryCondition ?: $defaultQueryCondition;
    }
}
