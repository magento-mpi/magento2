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
     * Get all options
     *
     * @return array
     */
    public function getOptions();

    /**
     * @param int $range
     * @param int[] $entityIds
     * @return array
     */
    public function getCount($range, array $entityIds);

    /**
     * Get range
     *
     * @return int
     */
    public function getRange();
}
