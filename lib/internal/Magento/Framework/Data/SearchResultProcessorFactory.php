<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Data;

/**
 * Class SearchResultProcessorFactory
 */
class SearchResultProcessorFactory
{
    const DEFAULT_INSTANCE_NAME = 'Magento\Framework\Data\SearchResultProcessor';

    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param string $instanceName
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param AbstractSearchResult $collection
     * @return SearchResultProcessor
     */
    public function create(AbstractSearchResult $collection)
    {
        return $this->objectManager->create(static::DEFAULT_INSTANCE_NAME, ['searchResult' => $collection]);
    }
}
