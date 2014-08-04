<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Request;

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
    }

    /**
     * Get Query Interface by name
     *
     * @param $queryName
     * @return \Magento\Framework\Search\Request\QueryInterface
     */
    public function get($queryName)
    {
        return $this->mapQuery($queryName);
    }

    /**
     * Convert array to Query instance
     *
     * @param string $queryName
     * @throws \Exception
     * @return \Magento\Framework\Search\Request\QueryInterface
     */
    private function mapQuery($queryName)
    {
        if (!isset($this->queries[$queryName])) {
            throw new \Exception('Query ' . $queryName . ' does not exists');
        }
        $query = $this->queries[$queryName];
        switch ($query['type']) {
            case 'matchQuery':
                $query = $this->objectManager->create(
                    'Magento\Framework\Search\Request\Query\Match',
                    [
                        'name' => $query['name'],
                        'boost' => isset($query['boost']) ? $query['boost'] : 1,
                        'matches' => $query['match']
                    ]
                );
                break;
            case 'filteredQuery':
                if (isset($query['queryReference'][0])) {
                    $reference = $this->mapQuery($query['queryReference'][0]['ref']);
                    $referenceType = \Magento\Framework\Search\Request\Query\Filter::REFERENCE_QUERY;
                } else {
                    if (isset($query['filterReference'][0])) {
                        $reference = $this->mapFilter($query['filterReference'][0]['ref']);
                        $referenceType = \Magento\Framework\Search\Request\Query\Filter::REFERENCE_FILTER;
                    } else {
                        throw new \Exception('Reference is not provided');
                    }
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
            case 'boolQuery':
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
     * @param $data
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
     * @param $data
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
     * @param $filterName
     * @throws \Exception
     * @return \Magento\Framework\Search\Request\FilterInterface
     */
    private function mapFilter($filterName)
    {
        if (!isset($this->filters[$filterName])) {
            throw new \Exception('Filter ' . $filterName . ' does not exists');
        }
        $filter = $this->filters[$filterName];
        switch ($filter['type']) {
            case 'termFilter':
                $filter = $this->objectManager->create(
                    'Magento\Framework\Search\Request\Filter\Term',
                    [
                        'name' => $filter['name'],
                        'field' => $filter['field'],
                        'value' => $filter['value']
                    ]
                );
                break;
            case 'rangeFilter':
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
            case 'boolFilter':
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
}