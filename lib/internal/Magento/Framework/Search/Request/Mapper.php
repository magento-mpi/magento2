<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Request;

use Magento\Framework\Exception\StateException;
use Magento\Framework\Search\Request\Query\Filter;

class Mapper
{
    /**
     * @var array
     */
    private $queries;

    /**
     * @var array
     */
    private $filters;

    /**
     * @var string[]
     */
    private $mappedQueries;

    /**
     * @var string[]
     */
    private $mappedFilters;

    /**
     * @var \Magento\Framework\ObjectManager
     */
    private $objectManager;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param array $queries
     * @param array $filters
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager,
        array $queries,
        array $filters = null
    ) {
        $this->objectManager = $objectManager;
        $this->queries = $queries;
        $this->filters = $filters;
        $this->mappedQueries = [];
        $this->mappedFilters = [];
    }

    /**
     * Get Query Interface by name
     *
     * @param string $queryName
     * @return QueryInterface
     * @throws StateException
     */
    public function get($queryName)
    {
        $query = $this->mapQuery($queryName);
        $this->validate();
        return $query;
    }

    /**
     * Convert array to Query instance
     *
     * @param string $queryName
     * @throws \Exception
     * @return QueryInterface
     */
    private function mapQuery($queryName)
    {
        if (!isset($this->queries[$queryName])) {
            throw new \Exception('Query ' . $queryName . ' does not exist');
        } elseif (in_array($queryName, $this->mappedQueries)) {
            throw new StateException('Cycle found. Query %1 already used in request hierarchy', [$queryName]);
        }
        $this->mappedQueries[] = $queryName;
        $query = $this->queries[$queryName];
        switch ($query['type']) {
            case QueryInterface::TYPE_MATCH:
                $query = $this->objectManager->create(
                    'Magento\Framework\Search\Request\Query\Match',
                    [
                        'name' => $query['name'],
                        'boost' => isset($query['boost']) ? $query['boost'] : 1,
                        'matches' => $query['match']
                    ]
                );
                break;
            case QueryInterface::TYPE_FILTER:
                if (isset($query['queryReference'][0])) {
                    $reference = $this->mapQuery($query['queryReference'][0]['ref']);
                    $referenceType = Filter::REFERENCE_QUERY;
                } elseif (isset($query['filterReference'][0])) {
                    $reference = $this->mapFilter($query['filterReference'][0]['ref']);
                    $referenceType = Filter::REFERENCE_FILTER;
                } else {
                    throw new \Exception('Reference is not provided');
                }
                $query = $this->objectManager->create(
                    'Magento\Framework\Search\Request\Query\Filter',
                    [
                        'name' => $query['name'],
                        'boost' => isset($query['boost']) ? $query['boost'] : 1,
                        'reference' => $reference,
                        'referenceType' => $referenceType
                    ]
                );
                break;
            case QueryInterface::TYPE_BOOL:
                $aggregatedByType = $this->aggregateQueriesByType($query['queryReference']);
                $query = $this->objectManager->create(
                    'Magento\Framework\Search\Request\Query\Bool',
                    array_merge(
                        ['name' => $query['name'], 'boost' => isset($query['boost']) ? $query['boost'] : 1],
                        $aggregatedByType
                    )
                );
                break;
            default:
                throw new \InvalidArgumentException('Invalid query type');
        }
        return $query;
    }

    /**
     * Aggregate Queries by clause
     *
     * @param array $data
     * @return array
     */
    private function aggregateQueriesByType($data)
    {
        $list = [];
        foreach ($data as $value) {
            $list[$value['clause']][$value['ref']] = $this->mapQuery($value['ref']);
        }
        return $list;
    }

    /**
     * Aggregate Filters by clause
     *
     * @param array $data
     * @return array
     */
    private function aggregateFiltersByType($data)
    {
        $list = [];
        foreach ($data as $value) {
            $list[$value['clause']][$value['ref']] = $this->mapFilter($value['ref']);
        }
        return $list;
    }

    /**
     * Convert array to Filter instance
     *
     * @param string $filterName
     * @throws \Exception
     * @return FilterInterface
     */
    private function mapFilter($filterName)
    {
        if (!isset($this->filters[$filterName])) {
            throw new \Exception('Filter ' . $filterName . ' does not exist');
        } elseif (in_array($filterName, $this->mappedFilters)) {
            throw new StateException('Cycle found. Filter %1 already used in request hierarchy', [$filterName]);
        }
        $this->mappedFilters[] = $filterName;
        $filter = $this->filters[$filterName];
        switch ($filter['type']) {
            case FilterInterface::TYPE_TERM:
                $filter = $this->objectManager->create(
                    'Magento\Framework\Search\Request\Filter\Term',
                    [
                        'name' => $filter['name'],
                        'field' => $filter['field'],
                        'value' => $filter['value']
                    ]
                );
                break;
            case FilterInterface::TYPE_RANGE:
                $filter = $this->objectManager->create(
                    'Magento\Framework\Search\Request\Filter\Range',
                    [
                        'name' => $filter['name'],
                        'field' => $filter['field'],
                        'from' => $filter['from'],
                        'to' => $filter['to']
                    ]
                );

                break;
            case FilterInterface::TYPE_BOOL:
                $aggregatedByType = $this->aggregateFiltersByType($filter['filterReference']);
                $filter = $this->objectManager->create(
                    'Magento\Framework\Search\Request\Filter\Bool',
                    array_merge(
                        ['name' => $filter['name']],
                        $aggregatedByType
                    )
                );
                break;
            default:
                throw new \InvalidArgumentException('Invalid filter type');
        }
        return $filter;
    }

    /**
     * @throws StateException
     */
    private function validate()
    {
        $this->validateQueries();
        $this->validateFilters();
    }

    /**
     * @throws StateException
     */
    private function validateQueries()
    {
        $allQueries = array_values($this->queries);
        $notUsedQueries = implode(', ', array_diff($allQueries, $this->mappedQueries));
        if (!empty($notUsedQueries)) {
            throw new StateException('Query %1 not used in request hierarchy', [$notUsedQueries]);
        }
    }

    /**
     * @throws StateException
     */
    private function validateFilters()
    {
        $allFilters = array_values($this->filters);
        $notUsedFilters = implode(', ', array_diff($allFilters, $this->mappedFilters));
        if (!empty($notUsedFilters)) {
            throw new StateException('Filter %1 not used in request hierarchy', [$notUsedFilters]);
        }
    }
}
