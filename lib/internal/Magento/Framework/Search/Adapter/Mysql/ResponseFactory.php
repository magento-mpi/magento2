<?php
/**
 * Response Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql;

use Magento\Framework\Search\QueryResponse;

class ResponseFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    /**
     * Document Factory
     *
     * @var DocumentFactory
     */
    protected $documentFactory;

    /**
     * Aggregation Factory
     *
     * @var AggregationFactory
     */
    protected $aggregationFactory;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param DocumentFactory $documentFactory
     * @param AggregationFactory $aggregationFactory
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager,
        DocumentFactory $documentFactory,
        AggregationFactory $aggregationFactory
    ) {
        $this->objectManager = $objectManager;
        $this->documentFactory = $documentFactory;
        $this->aggregationFactory = $aggregationFactory;
    }

    /**
     * Create Query Response instance
     *
     * @param mixed $rawResponse
     * @return QueryResponse
     */
    public function create($rawResponse)
    {
        $documents = array();
        foreach ($rawResponse['documents'] as $rawDocument) {
            /** @var \Magento\Framework\Search\Document[] $documents */
            $documents[] = $this->documentFactory->create($rawDocument);
        }
        /** @var \Magento\Framework\Search\Aggregation $aggregations */
        $aggregations = $this->documentFactory->create($rawResponse['aggregation']);
        return $this->objectManager->create(
            '\Magento\Framework\Search\QueryResponse',
            [
                'documents' => $documents,
                'aggregations' => $aggregations
            ]
        );
    }
}
