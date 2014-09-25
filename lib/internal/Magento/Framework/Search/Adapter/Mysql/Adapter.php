<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql;

use Magento\Framework\App\Resource;
use Magento\Framework\DB\Select;
use Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder\Container as AggregationContainer;
use Magento\Framework\Search\Adapter\Mysql\Aggregation\DataProviderContainer;
use Magento\Framework\Search\AdapterInterface;
use Magento\Framework\Search\EntityMetadata;
use Magento\Framework\Search\RequestInterface;

/**
 * MySQL Search Adapter
 */
class Adapter implements AdapterInterface
{
    /**
     * Mapper instance
     *
     * @var Mapper
     */
    protected $mapper;

    /**
     * Response Factory
     *
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var \Magento\Framework\App\Resource
     */
    private $resource;

    /**
     * @var DataProviderContainer
     */
    private $dataProviderContainer;

    /**
     * @var AggregationContainer
     */
    private $aggregationContainer;

    /**
     * @var EntityMetadata
     */
    private $entityMetadata;

    /**
     * @param Mapper $mapper
     * @param ResponseFactory $responseFactory
     * @param Resource $resource
     * @param DataProviderContainer $dataProviderContainer
     * @param AggregationContainer $aggregationContainer
     * @param EntityMetadata $entityMetadata
     */
    public function __construct(
        Mapper $mapper,
        ResponseFactory $responseFactory,
        Resource $resource,
        DataProviderContainer $dataProviderContainer,
        AggregationContainer $aggregationContainer,
        EntityMetadata $entityMetadata
    ) {
        $this->mapper = $mapper;
        $this->responseFactory = $responseFactory;
        $this->resource = $resource;
        $this->dataProviderContainer = $dataProviderContainer;
        $this->aggregationContainer = $aggregationContainer;
        $this->entityMetadata = $entityMetadata;
    }

    /**
     * {@inheritdoc}
     */
    public function query(RequestInterface $request)
    {
        /** @var Select $query */
        $query = $this->mapper->buildQuery($request);
        $documents = $this->executeQuery($query);

        $productIds = $this->getEntityIds($documents);
        $aggregations = $this->buildAggregations($request, $productIds);
        $response = [
            'documents' => $documents,
            'aggregations' => $aggregations,
        ];
        return $this->responseFactory->create($response);
    }

    /**
     * Executes query and return raw response
     *
     * @param Select $select
     * @return array
     */
    private function executeQuery(Select $select)
    {
        return $this->getConnection()->fetchAssoc($select);
    }

    /**
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private function getConnection()
    {
        return $this->resource->getConnection(Resource::DEFAULT_READ_RESOURCE);
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
     * @param RequestInterface $request
     * @param int[] $productIds
     * @return array
     */
    private function buildAggregations(RequestInterface $request, array $productIds)
    {
        $aggregations = [];
        $buckets = $request->getAggregation();
        $dataProvider = $this->dataProviderContainer->get($request->getIndex());
        foreach ($buckets as $bucket) {
            $aggregationBuilder = $this->aggregationContainer->get($bucket->getType());

            $select = $dataProvider->getDataSet($bucket, $request);
            $select = $aggregationBuilder->build($select, $bucket, $productIds);
            $aggregations[$bucket->getName()] = $this->executeQuery($select);
        }
        return $aggregations;
    }
}
