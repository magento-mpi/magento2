<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql;

use Magento\Framework\Search\Response\QueryResponse;

/**
 * Response Factory
 */
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
     * @return \Magento\Framework\Search\Response\QueryResponse
     */
    public function create($rawResponse)
    {
        $rawResponse = $this->prepareData($rawResponse);
        $documents = array();
        foreach ($rawResponse['documents'] as $rawDocument) {
            /** @var \Magento\Framework\Search\Document[] $documents */
            $documents[] = $this->documentFactory->create($rawDocument);
        }
        /** @var \Magento\Framework\Search\Response\Aggregation $aggregations */
        $aggregations = $this->aggregationFactory->create($rawResponse['aggregations']);
        return $this->objectManager->create(
            '\Magento\Framework\Search\QueryResponse',
            [
                'documents' => $documents,
                'aggregations' => $aggregations
            ]
        );
    }

    /**
     * Preparing
     *
     * @param array $rawResponse
     * @return array
     */
    private function prepareData(array $rawResponse)
    {
        $preparedResponse = [];
        $preparedResponse['documents'] = $this->prepareDocuments($rawResponse['documents']);
        $preparedResponse['aggregations'] = $this->prepareAggregations($rawResponse['aggregations']);
        return $preparedResponse;
    }

    /**
     * Prepare Documents
     *
     * @param array $rawDocumentList
     * @return array
     */
    private function prepareDocuments(array $rawDocumentList)
    {
        $documentList = [];
        foreach ($rawDocumentList as $document) {
            $documentFieldList = [];
            foreach ($document as $name => $values) {
                $documentFieldList[] = [
                    'name' => $name,
                    'value' => $values
                ];
            }
            $documentList[] = $documentFieldList;
        }
        return $documentList;
    }

    /**
     * Prepare Aggregations
     *
     * @param array $rawAggregations
     * @return array
     */
    private function prepareAggregations(array $rawAggregations)
    {
        return $rawAggregations; // Prepare aggregations here
    }
}
