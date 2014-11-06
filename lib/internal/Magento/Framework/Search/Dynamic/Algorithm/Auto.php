<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Dynamic\Algorithm;

use Magento\Framework\Search\Adapter\Mysql\Aggregation\DataProviderInterface;
use Magento\Framework\Search\Request\BucketInterface;

class Auto extends AbstractAlgorithm
{

    /**
     * {@inheritdoc}
     */
    public function getItems(BucketInterface $bucket, array $dimensions, array $entityIds)
    {
        $data = [];
        $range = $this->dataProvider->getRange();
        if (!$range) {
            $range = $this->getRange($bucket, $dimensions, $entityIds);
            $dbRanges = $this->dataProvider->getAggregation($bucket, $dimensions, $range, $entityIds, 'count');
            $data = $this->dataProvider->prepareData($range, $dbRanges);
        }

        return $data;
    }

    /**
     * @param DataProviderInterface $dataProvider
     * @param int[] $entityIds
     * @return number
     */
    private function getRange($bucket, array $dimensions ,array $entityIds)
    {
        $maxPrice = $this->getMaxPriceInt($entityIds);
        $index = 1;
        do {
            $range = pow(10, strlen(floor($maxPrice)) - $index);
            $items = $this->dataProvider->getAggregation($bucket, $dimensions, $range, $entityIds, 'count');
            $index++;
        } while ($range > $this->getMinRangePower() && count($items) < 2);

        return $range;
    }

    /**
     * Get maximum price from layer products set
     *
     * @param DataProviderInterface $dataProvider
     * @param int[] $entityIds
     * @return float
     */
    private function getMaxPriceInt(array $entityIds)
    {
        $aggregations = $this->dataProvider->getAggregations($entityIds);
        $maxPrice = $aggregations['max'];
        $maxPrice = floor($maxPrice);

        return $maxPrice;
    }

    /**
     * @param DataProviderInterface $dataProvider
     * @return int
     */
    private function getMinRangePower()
    {
        $options = $this->dataProvider->getOptions();

        return $options['min_range_power'];
    }
}
