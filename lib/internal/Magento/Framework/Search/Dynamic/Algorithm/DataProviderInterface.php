<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Dynamic\Algorithm;

interface DataProviderInterface
{
    /**
     * @param int[] $entityIds
     * @return array
     */
    public function getAggregations(array $entityIds);

    /**
     * Get interval division limit
     *
     * @return int
     */
    public function getIntervalDivisionLimit();
}
