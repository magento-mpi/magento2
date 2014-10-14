<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder;

class Container
{
    /**
     * @var BucketInterface[]
     */
    private $buckets;

    /**
     * @param BucketInterface[] $buckets
     */
    public function __construct(array $buckets)
    {
        $this->buckets = $buckets;
    }

    /**
     * @param string $bucketType
     * @return BucketInterface
     */
    public function get($bucketType)
    {
        return $this->buckets[$bucketType];
    }
}
