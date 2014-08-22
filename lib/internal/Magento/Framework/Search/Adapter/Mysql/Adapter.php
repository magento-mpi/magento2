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

use Magento\Framework\Search\Adapter\Mysql\ResponseConverter;
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
     * @var ResponseConverter
     */
    private $converter;

    /**
     * @param Mapper $mapper
     * @param ResponseFactory $responseFactory
     * @param ResponseConverter $converter
     */
    public function __construct(
        Mapper $mapper,
        ResponseFactory $responseFactory,
        ResponseConverter $converter
    ) {
        $this->mapper = $mapper;
        $this->responseFactory = $responseFactory;
        $this->converter = $converter;
    }

    /**
     * {@inheritdoc}
     */
    public function query(RequestInterface $request)
    {
        $query = $this->mapper->buildQuery($request);
        $documentExecuteResult = $this->executeQuery($query);
        $response = $this->prepareToResponse($documentExecuteResult);
        return $this->responseFactory->create($response);
    }

    /**
     * Executes query and return raw response
     *
     * @return mixed
     */
    private function executeQuery()
    {
    }

    /**
     * Prepare data to Response
     *
     * @param array $documentExecuteResult result after executeQuery
     * @return array
     */
    private function prepareToResponse($documentExecuteResult)
    {
        $response = [];
        $response['documents'] = $this->converter->convertToDocument($documentExecuteResult);
        $response['aggregation'] = [];
        return $response;
    }
}
