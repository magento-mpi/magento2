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
     * @param Mapper $mapper
     * @param ResponseFactory $responseFactory
     */
    public function __construct(
        Mapper $mapper,
        ResponseFactory $responseFactory
    ) {
        $this->mapper = $mapper;
        $this->responseFactory = $responseFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function query(RequestInterface $request)
    {
        $query = $this->mapper->buildQuery($request);
        $response = $this->executeQuery($query);
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
}
