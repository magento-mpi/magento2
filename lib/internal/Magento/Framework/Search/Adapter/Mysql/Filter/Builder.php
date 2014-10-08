<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql\Filter;

use Magento\Eav\Model\Config;
use Magento\Framework\App\Resource;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Search\Adapter\Mysql\Filter\Builder\Range;
use Magento\Framework\Search\Adapter\Mysql\Filter\Builder\Term;
use Magento\Framework\Search\Adapter\Mysql\ConditionManager;
use Magento\Framework\Search\Adapter\Mysql\Filter\Builder\Wildcard;
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
     * @var ConditionManager
     */
    private $conditionManager;

    /**
     * @var Wildcard
     */
    private $wildcard;
    /**
     * @var ScopeResolverInterface
     */
    private $scopeResolver;
    /**
     * @var Resource
     */
    private $resource;
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Range $range
     * @param Term $term
     * @param Wildcard $wildcard
     * @param ConditionManager $conditionManager
     * @param ScopeResolverInterface $scopeResolver
     * @param Resource $resource
     * @param Config $config
     */
    public function __construct(
        Range $range,
        Term $term,
        Wildcard $wildcard,
        ConditionManager $conditionManager,
        ScopeResolverInterface $scopeResolver,
        Resource $resource,
        Config $config
    ) {
        $this->range = $range;
        $this->term = $term;
        $this->conditionManager = $conditionManager;
        $this->wildcard = $wildcard;
        $this->scopeResolver = $scopeResolver;
        $this->resource = $resource;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function build(RequestFilterInterface $filter, $conditionType)
    {
        return $this->processFilter($filter, $this->isNegation($conditionType));
    }

    /**
     * @return Select
     */
    private function getSelect()
    {
        return $this->getConnection()->select();
    }

    /**
     * @return AdapterInterface
     */
    private function getConnection()
    {
        return $this->resource->getConnection(Resource::DEFAULT_READ_RESOURCE);
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
            case RequestFilterInterface::TYPE_WILDCARD:
                /** @var \Magento\Framework\Search\Request\Filter\Wildcard $filter */
                $query = $this->wildcard->buildFilter($filter, $isNegation);
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Unknown filter type \'%s\'', $filter->getType()));
        }

        $currentStoreId  = $this->scopeResolver->getScope()->getId();

        $attribute = $this->config->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $filter->getField());
        $select = $this->getSelect();
        $table = $attribute->getBackendTable();
        if ($filter->getField() == 'price') {
            $select->from(['main_table' => $this->resource->getTable('catalog_product_index_price')], 'entity_id')
                ->where($query);
        } else  if ($attribute->isStatic()) {
            $select->from(['main_table' => $table], 'entity_id')
                ->where($query);
        } else {

            $ifNullCondition = $this->getConnection()->getIfNullSql('current_store.value', 'main_table.value');

            $select->from(['main_table' => $table], 'entity_id')
                ->joinLeft(
                    ['current_store' => $table],
                    'current_store.attribute_id = main_table.attribute_id AND current_store.store_id = '
                    . $currentStoreId,
                    null
                )
                ->columns([$filter->getField() => $ifNullCondition])
                ->where(
                    'main_table.attribute_id = ?',
                    $attribute->getAttributeId()
                )
                ->where('main_table.store_id = ?', 0)
                ->having($query);
        }


        return  'product_id '. ( $isNegation ? 'NOT' : '' ) .' IN (
                select entity_id from  ' . $this->conditionManager->wrapBrackets($select) . '
             as filter)';
       ;
    }

    /**
     * @param string $conditionType
     * @return bool
     */
    private function isNegation($conditionType)
    {
        return Bool::QUERY_CONDITION_NOT === $conditionType;
    }

    /**
     * @param RequestFilterInterface|\Magento\Framework\Search\Request\Filter\Bool $filter
     * @param bool $isNegation
     * @return string
     */
    private function processBoolFilter(RequestFilterInterface $filter, $isNegation)
    {
        $must = $this->buildFilters($filter->getMust(), Select::SQL_AND, $isNegation);
        $should = $this->buildFilters($filter->getShould(), Select::SQL_OR, $isNegation);
        $mustNot = $this->buildFilters(
            $filter->getMustNot(),
            Select::SQL_AND,
            !$isNegation
        );

        $queries = [
            $must,
            $this->conditionManager->wrapBrackets($should),
            $this->conditionManager->wrapBrackets($mustNot),
        ];

        return $this->conditionManager->combineQueries($queries, Select::SQL_AND);
    }

    /**
     * @param RequestFilterInterface|\Magento\Framework\Search\Request\Filter\Term $filter
     * @param bool $isNegation
     * @return string
     */
    private function processTermFilter(RequestFilterInterface $filter, $isNegation)
    {
        return $this->term->buildFilter($filter, $isNegation);
    }

    /**
     * @param RequestFilterInterface|\Magento\Framework\Search\Request\Filter\Range $filter
     * @param bool $isNegation
     * @return string
     */
    private function processRangeFilter(RequestFilterInterface $filter, $isNegation)
    {
        return $this->range->buildFilter($filter, $isNegation);
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
        return $this->conditionManager->combineQueries($queries, $unionOperator);
    }
}
