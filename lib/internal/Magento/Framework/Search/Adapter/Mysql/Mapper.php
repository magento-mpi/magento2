<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql;

use Magento\Framework\App\Resource\Config;
use Magento\Framework\DB\Select;
use Magento\Framework\Search\Adapter\Mysql\Filter\Builder;
use Magento\Framework\Search\Adapter\Mysql\Query\Builder\Match as MatchQueryBuilder;
use Magento\Framework\Search\Request\Query\Bool as BoolQuery;
use Magento\Framework\Search\Request\Query\Filter as FilterQuery;
use Magento\Framework\Search\Request\Query\Match as MatchQuery;
use Magento\Framework\Search\Request\QueryInterface as RequestQueryInterface;
use Magento\Framework\Search\Request\QueryInterface;
use Magento\Framework\Search\RequestInterface;

/**
 * Mapper class. Maps library request to specific adapter dependent query
 */
class Mapper
{
    const BOOL_MUST_NOT = true;
    const BOOL_MUST = false;

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
     * @var Filter\Builder
     */
    private $filterBuilder;

    /**
     * @param \Magento\Framework\App\Resource $resource
     * @param ScoreBuilderFactory $scoreBuilderFactory
     * @param MatchQueryBuilder $matchQueryBuilder
     * @param Builder $filterBuilder
     */
    public function __construct(
        \Magento\Framework\App\Resource $resource,
        ScoreBuilderFactory $scoreBuilderFactory,
        MatchQueryBuilder $matchQueryBuilder,
        Builder $filterBuilder
    ) {
        $this->resource = $resource;
        $this->scoreBuilderFactory = $scoreBuilderFactory;
        $this->matchQueryBuilder = $matchQueryBuilder;
        $this->filterBuilder = $filterBuilder;
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
        $select = $this->processQuery($scoreBuilder, $request->getQuery(), $this->getSelect(), self::BOOL_MUST);
        return $select;
    }

    /**
     * Process query
     *
     * @param ScoreBuilder $scoreBuilder
     * @param RequestQueryInterface $query
     * @param Select $select
     * @param bool $isNot
     * @return Select
     * @throws \InvalidArgumentException
     */
    protected function processQuery(
        ScoreBuilder $scoreBuilder,
        RequestQueryInterface $query,
        Select $select,
        $isNot
    ) {
        switch ($query->getType()) {
            case RequestQueryInterface::TYPE_MATCH:
                /** @var MatchQuery $query */
                $scoreBuilder->startQuery();
                $select = $this->matchQueryBuilder->build(
                    $scoreBuilder,
                    $select,
                    $query,
                    $isNot
                );
                $scoreBuilder->endQuery($query->getBoost());
                break;
            case RequestQueryInterface::TYPE_BOOL:
                /** @var BoolQuery $query */
                $select = $this->processBoolQuery($scoreBuilder, $query, $select);
                break;
            case RequestQueryInterface::TYPE_FILTER:
                /** @var FilterQuery $query */
                $select = $this->processFilterQuery($scoreBuilder, $query, $select, $isNot);
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
     * @return Select
     */
    private function processBoolQuery(ScoreBuilder $scoreBuilder, BoolQuery $query, Select $select)
    {
        $scoreBuilder->startQuery();

        $select = $this->processBoolQueryCondition(
            $scoreBuilder,
            $query->getMust(),
            $select,
            self::BOOL_MUST
        );

        $select = $this->processBoolQueryCondition(
            $scoreBuilder,
            $query->getShould(),
            $select,
            self::BOOL_MUST
        );

        $select = $this->processBoolQueryCondition(
            $scoreBuilder,
            $query->getMustNot(),
            $select,
            self::BOOL_MUST_NOT
        );

        $scoreBuilder->endQuery($query->getBoost());

        return $select;
    }

    /**
     * Process bool query condition (must, should, must_not)
     *
     * @param ScoreBuilder $scoreBuilder
     * @param QueryInterface[] $subQueryList
     * @param Select $select
     * @param bool $isNot
     * @return Select
     */
    private function processBoolQueryCondition(
        ScoreBuilder $scoreBuilder,
        array $subQueryList,
        Select $select,
        $isNot
    ) {
        foreach ($subQueryList as $subQuery) {
            $select = $this->processQuery($scoreBuilder, $subQuery, $select, $isNot);
        }
        return $select;
    }

    /**
     * Process filter query
     *
     * @param ScoreBuilder $scoreBuilder
     * @param FilterQuery $query
     * @param Select $select
     * @param bool $isNot
     * @return Select
     */
    private function processFilterQuery(ScoreBuilder $scoreBuilder, FilterQuery $query, Select $select, $isNot)
    {
        switch ($query->getReferenceType()) {
            case FilterQuery::REFERENCE_QUERY:
                $scoreBuilder->startQuery();
                $select = $this->processQuery($scoreBuilder, $query->getReference(), $select, $isNot);
                $scoreBuilder->endQuery($query->getBoost());
                break;
            case FilterQuery::REFERENCE_FILTER:
                $filterCondition = $this->filterBuilder->build($query->getReference());
                if ($isNot === true) {
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
}
