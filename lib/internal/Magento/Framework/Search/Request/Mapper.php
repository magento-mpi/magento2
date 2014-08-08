<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Request;

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
     * @var array
     */
    private $aggregation;

    /**
     * @var \Magento\Framework\ObjectManager
     */
    private $objectManager;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param array $queries
     * @param array $aggregation
     * @param array $filters
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager,
        array $queries,
        array $aggregation,
        array $filters = null
    ) {
        $this->objectManager = $objectManager;
        $this->queries = $queries;
        $this->aggregation = $aggregation;
        $this->filters = $filters;
    }

    /**
     * Get Query Interface by name
     *
     * @param string $queryName
     * @return QueryInterface
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
     * @return QueryInterface
     */
    private function mapQuery($queryName)
    {
        if (!isset($this->queries[$queryName])) {
            throw new \Exception('Query ' . $queryName . ' does not exist');
        }
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
        }
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
     * Build BucketInterface[] from array
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getBuckets()
    {
        $buckets = array();
        foreach ($this->aggregation as $bucketData) {
            $arguments =
            [
                'name' => $bucketData['name'],
                'field' => $bucketData['field'],
                'metrics' => $this->mapMetrics($bucketData['metric'])
            ];
            switch ($bucketData['type']) {
                case BucketInterface::TYPE_TERM:
                    $bucket = $this->objectManager->create(
                        'Magento\Framework\Search\Request\Aggregation\TermBucket',
                        $arguments
                    );
                    break;
                case BucketInterface::TYPE_RANGE:
                    $bucket = $this->objectManager->create(
                        'Magento\Framework\Search\Request\Aggregation\RangeBucket',
                        array_merge(
                            $arguments,
                            ['ranges' => $this->mapRanges($bucketData['range'])]
                        )
                    );
                    break;
                default:
                    throw new \InvalidArgumentException('Invalid bucket type');
            }
            $buckets[] = $bucket;
        }
        return $buckets;
    }

    /**
     * Build Metric[] from array
     *
     * @param array $metrics
     * @return array
     */
    private function mapMetrics(array $metrics)
    {
        $metricObjects = array();
        foreach ($metrics as $metric) {
            $metricObjects[] = $this->objectManager->create(
                'Magento\Framework\Search\Request\Aggregation\Metric',
                [
                    'type' => $metric['type']
                ]
            );
        }
        return $metricObjects;
    }

    /**
     * Build Range[] from array
     *
     * @param array $ranges
     * @return array
     */
    private function mapRanges(array $ranges)
    {
        $rangeObjects = array();
        foreach ($ranges as $range) {
            $rangeObjects[] = $this->objectManager->create(
                'Magento\Framework\Search\Request\Aggregation\Range',
                [
                    'from' => $range['from'],
                    'to' => $range['to']
                ]
            );
        }
        return $rangeObjects;
    }
}
