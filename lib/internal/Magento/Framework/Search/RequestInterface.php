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
     * Get Size
     *
     * @return int|null
     */
    public function getSize();
}
