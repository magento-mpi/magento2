<?php
/**
 * MySQL Search Adapter
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql;

use Magento\Framework\App\Resource\Config;
use Magento\Framework\DB\Select;
use Magento\Framework\Search\AdapterInterface;
use Magento\Framework\Search\RequestInterface;

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
        \Magento\Framework\App\Resource $resource
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
            'documents' => $this->executeDocumentsQuery($query),
            'aggregations' => $this->executeAggregationsQuery($query),
        ];
        return $this->responseFactory->create($response);
    }

    /**
     * Executes query and return raw response
     * @param Select $select
     *
     * @return array
     */
    private function executeDocumentsQuery(Select $select)
    {
        $dbAdapter = $this->resource->getConnection(\Magento\Framework\App\Resource::DEFAULT_READ_RESOURCE);
        return $dbAdapter->fetchAssoc($select);
    }

    /**
     * @param Select $query
     * @return array
     */
    private function executeAggregationsQuery(Select $query)
    {
        return [];
    }
}
