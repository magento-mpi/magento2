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
     * @var FilterInterface[]
     */
    protected $filters;

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
     * @var int
     */
    protected $from;

    /**
     * @var int
     */
    protected $to;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @param string $name
     * @param string $indexName
     * @param FilterInterface[] $filters
     * @param BucketInterface[] $buckets
     * @param QueryInterface $query
     * @param int|null $from
     * @param int|null $to
     * @param int|null $limit
     */
    public function __construct(
        $name,
        $indexName,
        array $filters,
        array $buckets,
        QueryInterface $query,
        $from = null,
        $to = null,
        $limit = null
    ) {
        $this->name = $name;
        $this->index =$indexName;
        $this->filters = $filters;
        $this->buckets = $buckets;
        $this->query = $query;
        $this->from = $from;
        $this->to = $to;
        $this->limit = $limit;
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
    public function getFilter($filterName = null)
    {
        if (isset($filterName)) {
            return $this->filters[$filterName];
        }
        return $this->filters;
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
    public function getTo()
    {
        return $this->to;
    }

    /**
     * {@inheritdoc}
     */
    public function getLimit()
    {
        return $this->limit;
    }
}
