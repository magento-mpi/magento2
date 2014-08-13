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
     * @var \Magento\Framework\ObjectManager
     */
    protected $documentFactory;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param DocumentFactory $documentFactory
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager,
        DocumentFactory $documentFactory
    ) {
        $this->objectManager = $objectManager;
        $this->documentFactory = $documentFactory;
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
        foreach($rawResponse as $rawDocument) {
            /** @var \Magento\Framework\Search\Document[] $documents */
            $documents[] = $this->documentFactory->create($rawDocument);
        }
        return $this->objectManager->create('\Magento\Framework\Search\QueryResponse', $documents);
    }
}
