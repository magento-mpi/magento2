<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql;

/**
 * Aggregation Factory
 */
class AggregationFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Create Aggregation instance
     *
     * @param mixed $rawAggregation
     * @return \Magento\Framework\Search\Aggregation
     */
    public function create($rawAggregation)
    {
        $buckets = array();
        foreach ($rawAggregation as $rawBucket) {
            /** @var \Magento\Framework\Search\Bucket[] $buckets */
            $buckets[] = $this->objectManager->create(
                '\Magento\Framework\Search\Bucket',
                [
                    $rawBucket['name'],
                    $rawBucket['value']
                ]
            );
        }
        return $this->objectManager->create('\Magento\Framework\Search\Aggregation', ['buckets' => $buckets]);
    }
}
