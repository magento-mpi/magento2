<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search;

/**
 * Faceted data
 */
class Aggregation implements \IteratorAggregate
{
    /**
     * Buckets array
     *
     * @var Bucket[]
     */
    protected $buckets;

    /**
     * @param Bucket[] $buckets
     */
    public function __construct(array $buckets)
    {
        $this->buckets = $buckets;
    }

    /**
     * Implementation of \IteratorAggregate::getIterator()
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->buckets);
    }

    /**
     * Get Document field
     *
     * @param string $bucketName
     * @return Bucket
     */
    public function getBucket($bucketName)
    {
        return $this->buckets[$bucketName];
    }

    /**
     * Get Document field names
     *
     * @return array
     */
    public function getBucketNames()
    {
        return array_keys($this->buckets);
    }
}
