<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql\Aggregation;

use Magento\Framework\App\Resource;
use Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder\Container as AggregationContainer;
use Magento\Framework\Search\EntityMetadata;
use Magento\Framework\Search\RequestInterface;
use Magento\Framework\DB\Select;

class Builder
{
    /**
     * @var DataProviderContainer
     */
    private $dataProviderContainer;

    /**
     * @var Builder\Container
     */
    private $aggregationContainer;

    /**
     * @var EntityMetadata
     */
    private $entityMetadata;

    /**
     * @var Resource
     */
    private $resource;

    public function __construct(
        Resource $resource,
        DataProviderContainer $dataProviderContainer,
        AggregationContainer $aggregationContainer,
        EntityMetadata $entityMetadata
    ) {
        $this->dataProviderContainer = $dataProviderContainer;
        $this->aggregationContainer = $aggregationContainer;
        $this->entityMetadata = $entityMetadata;
        $this->resource = $resource;
    }

    /**
     * @param RequestInterface $request
     * @param int[] $documents
     * @return array
     */
    public function build(RequestInterface $request, array $documents)
    {
        $entityIds = $this->getEntityIds($documents);

        return $this->processAggregations($request, $entityIds);
    }

    /**
     * @param array $products
     * @return int[]
     */
    private function getEntityIds($products)
    {
        $fieldName = $this->entityMetadata->getEntityId();
        $productIds = [];
        foreach ($products as $product) {
            $productIds[] = $product[$fieldName];
        }
        return $productIds;
    }

    /**
     * Executes query and return raw response
     *
     * @param Select $select
     * @return array
     */
    private function executeQuery(Select $select)
    {
        return $this->resource->getConnection(Resource::DEFAULT_READ_RESOURCE)->fetchAssoc($select);
    }

    /**
     * @param RequestInterface $request
     * @param int[] $entityIds
     * @return array
     */
    private function processAggregations(RequestInterface $request, array $entityIds)
    {
        $aggregations = [];
        $buckets = $request->getAggregation();
        $dataProvider = $this->dataProviderContainer->get($request->getIndex());
        foreach ($buckets as $bucket) {
            $aggregationBuilder = $this->aggregationContainer->get($bucket->getType());

            $select = $dataProvider->getDataSet($bucket, $request);
            $select = $aggregationBuilder->build($select, $bucket, $entityIds);
            $aggregations[$bucket->getName()] = $this->executeQuery($select);
        }
        return $aggregations;
    }
}
