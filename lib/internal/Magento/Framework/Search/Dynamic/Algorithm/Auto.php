<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Dynamic\Algorithm;

use Magento\Framework\Search\Adapter\Mysql\Aggregation\DataProviderInterface;

class Auto implements AlgorithmInterface
{

    /**
     * {@inheritdoc}
     */
    public function getItems(DataProviderInterface $dataProvider, array $entityIds, array $intervals)
    {
        $data = [];
        if (empty($intervals)) {
            $range = $dataProvider->getRange();
            if (!$range) {
                $range = $this->getRange($dataProvider, $entityIds);
                $dbRanges = $dataProvider->getCount($range, $entityIds);
                $data = $dataProvider->prepareData($range, $dbRanges);
            }
        }

        return $data;
    }

    /**
     * @param DataProviderInterface $dataProvider
     * @param int[] $entityIds
     * @return number
     */
    private function getRange(DataProviderInterface $dataProvider, array $entityIds)
    {
        $maxPrice = $this->getMaxPriceInt($dataProvider, $entityIds);
        $index = 1;
        do {
            $range = pow(10, strlen(floor($maxPrice)) - $index);
            $items = $dataProvider->getCount($range, $entityIds);
            $index++;
        } while ($range > $this->getMinRangePower($dataProvider) && count($items) < 2);

        return $range;
    }

    /**
     * Get maximum price from layer products set
     *
     * @param DataProviderInterface $dataProvider
     * @param int[] $entityIds
     * @return float
     */
    private function getMaxPriceInt(DataProviderInterface $dataProvider, array $entityIds)
    {
        $aggregations = $dataProvider->getAggregations($entityIds);
        $maxPrice = $aggregations['max'];
        $maxPrice = floor($maxPrice);

        return $maxPrice;
    }

    /**
     * @param DataProviderInterface $dataProvider
     * @return int
     */
    private function getMinRangePower(DataProviderInterface $dataProvider)
    {
        $options = $dataProvider->getOptions();

        return $options['min_range_power'];
    }
}
