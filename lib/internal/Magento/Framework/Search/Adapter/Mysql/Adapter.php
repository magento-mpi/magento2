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
use Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder\Term;
use Magento\Framework\Search\Adapter\Mysql\Aggregation\DataProviderInterface;
use Magento\Framework\Search\AdapterInterface;
use Magento\Framework\Search\Request\BucketInterface;
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
     * @var Aggregation\Builder\Term
     */
    private $termBuilder;
    /**
     * @var DataProviderInterface
     */
    private $dataProvider;

    /**
     * @param Mapper $mapper
     * @param ResponseFactory $responseFactory
     * @param Resource $resource
     * @param Aggregation\Builder\Term $termBuilder
     * @param DataProviderInterface $dataProvider
     */
    public function __construct(
        Mapper $mapper,
        ResponseFactory $responseFactory,
        Resource $resource,
        Term $termBuilder,
        DataProviderInterface $dataProvider
    ) {
        $this->mapper = $mapper;
        $this->responseFactory = $responseFactory;
        $this->resource = $resource;
        $this->termBuilder = $termBuilder;
        $this->dataProvider = $dataProvider;
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
        $productIds = [];
        foreach ($products as $product) {
            $productIds[] = $product['product_id'];
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
        foreach ($buckets as $bucket) {
            switch ($bucket->getType()) {
                case BucketInterface::TYPE_TERM:
                    $select = $this->dataProvider->getTermDataSet($bucket, $request);
                    $select = $this->termBuilder->build($select, $bucket, $productIds);
                    $aggregations[$bucket->getName()] = $this->executeQuery($select);
                    break;
            }
        }
        return $aggregations;
    }
}
