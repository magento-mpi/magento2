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

use Magento\Framework\App\Resource;
use Magento\Framework\App\Resource\Config;
use Magento\Framework\DB\Select;
use Magento\Framework\Search\Request\Query\Bool as BoolQuery;
use Magento\Framework\Search\Request\Query\Match as MatchQuery;
use Magento\Framework\Search\Request\QueryInterface;
use Magento\Framework\Search\RequestInterface;

class Mapper
{
    /**
     * @var Resource
     */
    private $resource;
    /**
     * @var ScoreManager
     */
    private $scoreManager;

    public function __construct(
        Resource $resource,
        ScoreManager $scoreManager
    ) {
        $this->resource = $resource;
        $this->scoreManager = $scoreManager;
    }

    /**
     * Build adapter dependent query
     *
     * @param RequestInterface $request
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return Select
     */
    public function buildQuery(RequestInterface $request)
    {
        $select = $this->processQuery($request->getQuery(), $this->getSelect());
        $this->scoreManager->clear();
        return $select;
    }

    /**
     * @param QueryInterface $query
     * @param Select $select
     * @param string|bool $queryType
     * @return Select
     */
    protected function processQuery(QueryInterface $query, Select $select, $queryType = false)
    {
        switch ($query->getType()) {
            case QueryInterface::TYPE_MATCH:
                /** @var MatchQuery $query */
//                $matchQueryBuilder->buildQuery(
//                    $select,
//                    $query,
//                    $this->getFilteredQueryType($queryType, BoolQuery::QUERY_TYPE_MUST)
//                );
                break;
            case QueryInterface::TYPE_BOOL:
                /** @var BoolQuery $query */
                $this->processBoolQuery($query, $select, $queryType);
                break;
            case QueryInterface::TYPE_FILTER:
                /** @var \Magento\Framework\Search\Request\Query\Filter $query */
//                $filterQueryBuilder->buildQuery(
//                    $select,
//                    $query,
//                    $this->getFilteredQueryType($queryType, BoolQuery::QUERY_TYPE_MUST)
//                );
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Unknown query type \'%s\'', $query->getType()));
        }
        return $select;
    }

    /**
     * @return Select
     */
    private function getSelect()
    {
        return $this->resource->getConnection(Config::DEFAULT_SETUP_CONNECTION)->select();
    }

    /**
     * @param BoolQuery $query
     * @param Select $select
     * @param $queryType
     */
    private function processBoolQuery(BoolQuery $query, Select $select, $queryType)
    {
        foreach ($query->getMust() as $subQuery) {
            $this->processQuery(
                $subQuery,
                $select,
                $this->getFilteredQueryType($queryType, BoolQuery::QUERY_CONDITION_MUST)
            );
        }
        foreach ($query->getShould() as $subQuery) {
            $this->processQuery(
                $subQuery,
                $select,
                $this->getFilteredQueryType($queryType, BoolQuery::QUERY_CONDITION_SHOULD)
            );
        }
        foreach ($query->getMustNot() as $subQuery) {
            $this->processQuery(
                $subQuery,
                $select,
                $this->getFilteredQueryType($queryType, BoolQuery::QUERY_CONDITION_NOT)
            );
        }
    }

    private function getFilteredQueryType($queryType, $defaultQueryType = BoolQuery::QUERY_CONDITION_MUST)
    {
        return $queryType ?: $defaultQueryType;
    }
}
