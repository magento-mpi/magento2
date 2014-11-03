<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql\Aggregation;

use Magento\Framework\DB\Select;
use Magento\Framework\Search\Request\BucketInterface;
use Magento\Framework\Search\Request\Dimension;

interface DataProviderInterface
{
    /**
     * @param BucketInterface $bucket
     * @param Dimension[] $dimensions
     * @return Select
     */
    public function getDataSet(BucketInterface $bucket, array $dimensions);

    /**
     * Executes query and return raw response
     *
     * @param Select $select
     * @return array
     */
    public function execute(Select $select);

    /**
     * @param int[] $entityIds
     * @return array
     */
    public function getAggregations(array $entityIds);

    /**
     * Get all options
     *
     * @return array
     */
    public function getOptions();

    /**
     * @param int $range
     * @param int[] $entityIds
     * @param string $aggregationType
     * @return array
     */
    public function getAggregation($range, array $entityIds, $aggregationType);

    /**
     * @param int $range
     * @param array $dbRanges
     * @return array
     */
    public function prepareData($range, array $dbRanges);

    /**
     * Get range
     *
     * @return int
     */
    public function getRange();
}
