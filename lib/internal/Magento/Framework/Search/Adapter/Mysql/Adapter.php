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
use Magento\Framework\Search\RequestInterface;
use Magento\Framework\Search\AdapterInterface;

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
     * @param Mapper $mapper
     * @param ResponseFactory $responseFactory
     * @param \Magento\Framework\App\Resource $resource
     */
    public function __construct(
        Mapper $mapper,
        ResponseFactory $responseFactory,
        Resource $resource
    ) {
        $this->mapper = $mapper;
        $this->responseFactory = $responseFactory;
        $this->resource = $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function query(RequestInterface $request)
    {
        /** @var Select $query */
        $query = $this->mapper->buildQuery($request);
        $response = [
            'documents' => $this->executeDocuments($query),
            'aggregations' => $this->executeAggregations($query),
        ];
        return $this->responseFactory->create($response);
    }

    /**
     * Executes query and return raw response
     * @param Select $select
     *
     * @return array
     */
    private function executeDocuments(Select $select)
    {
        return $this->getConnection()->fetchAssoc($select);
    }

    /**
     * @param Select $select
     * @return array
     */
    private function executeAggregations(Select $select)
    {
        return [];
    }

    /**
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private function getConnection()
    {
        return $this->resource->getConnection(Resource::DEFAULT_READ_RESOURCE);
    }
}
