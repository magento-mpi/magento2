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

interface RequestInterface
{
    /**
     * Get Name
     *
     * @return string
     */
    public function getName();

    /**
     * Get Index name
     *
     * @return string
     */
    public function getIndex();

    /**
     * Get Filter
     *
     * @param string $filterName
     * @return FilterInterface|FilterInterface[]
     */
    public function getFilter($filterName = null);

    /**
     * Get Aggregation Buckets
     *
     * @return BucketInterface[]
     */
    public function getAggregation();

    /**
     * Get Main Request Query
     *
     * @return QueryInterface
     */
    public function getQuery();

    /**
     * Get From
     *
     * @return int|null
     */
    public function getFrom();

    /**
     * Get To
     *
     * @return int|null
     */
    public function getTo();

    /**
     * Get Limit
     *
     * @return int|null
     */
    public function getLimit();
}
