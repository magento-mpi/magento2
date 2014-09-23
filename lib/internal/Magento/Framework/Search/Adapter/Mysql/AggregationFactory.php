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
     * @return \Magento\Framework\Search\Response\Aggregation
     */
    public function create($rawAggregation)
    {
        $buckets = array();
        foreach ($rawAggregation as $rawBucket) {
            /** @var \Magento\Framework\Search\Response\Bucket[] $buckets */
            $buckets[] = $this->objectManager->create(
                'Magento\Framework\Search\Response\Bucket',
                [
                    $rawBucket['name'],
                    $this->prepareValues((array)$rawBucket['values'])
                ]
            );
        }
        return $this->objectManager->create('\Magento\Framework\Search\Response\Aggregation', ['buckets' => $buckets]);
    }

    /**
     * Prepare values list
     *
     * @param array $values
     * @return \Magento\Framework\Search\Response\Aggregation\Value[]
     */
    private function prepareValues(array $values)
    {
        $valuesObjects = [];
        foreach ($values as $value) {
            $valuesObjects[] = $this->objectManager->create(
                '\Magento\Framework\Search\Response\Aggregation\Value',
                [
                    'value' => $value['value'],
                    'metrics' => $value['metrics'],
                ]
            );
        }
        return $valuesObjects;
    }
}
