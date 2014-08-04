<?php
/**
 * Search Request
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search;

use Magento\Framework\Search\Request\FilterInterface;
use Magento\Framework\Search\Request\BucketInterface;
use Magento\Framework\Search\Request\QueryInterface;

class Request implements RequestInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $index;

    /**
     * @var BucketInterface[]
     */
    protected $buckets;

    /**
     * Main query which represents the whole query hierarchy
     *
     * @var QueryInterface
     */
    protected $query;

    /**
     * @var int|null
     */
    protected $from;

    /**
     * @var int|null
     */
    protected $size;

    /**
     * @param string $name
     * @param string $indexName
     * @param QueryInterface $query
     * @param int|null $from
     * @param int|null $size
     * @param BucketInterface[] $buckets
     */
    public function __construct(
        $name,
        $indexName,
        QueryInterface $query,
        $from = null,
        $size = null,
        array $buckets = null
    ) {
        $this->name = $name;
        $this->index =$indexName;
        $this->buckets = $buckets;
        $this->query = $query;
        $this->from = $from;
        $this->size = $size;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * {@inheritdoc}
     */
    public function getAggregation()
    {
        return $this->buckets;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * {@inheritdoc}
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return $this->size;
    }
}
