<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Dynamic\Algorithm;

class Auto implements AlgorithmInterface
{
    /**
     * @var DataProviderInterface
     */
    private $dataProvider;

    /**
     * @param DataProviderInterface $dataProvider
     */
    public function __construct(DataProviderInterface $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(array $entityIds, array $intervals)
    {
        $data = [];
        if (empty($intervals)) {
            $range = $this->dataProvider->getRange();
            if (!$range) {
                $range = $this->getRange($entityIds);
                $dbRanges = $this->dataProvider->getCount($range, $entityIds);
                $data = $this->prepareData($range, $dbRanges);
            }
        }

        return $data;
    }

    /**
     * @param int[] $entityIds
     * @return number
     */
    private function getRange(array $entityIds)
    {
        $maxPrice = $this->getMaxPriceInt($entityIds);
        $index = 1;
        do {
            $range = pow(10, strlen(floor($maxPrice)) - $index);
            $items = $this->dataProvider->getCount($range, $entityIds);
            $index++;
        } while ($range > $this->getMinRangePower() && count($items) < 2);

        return $range;
    }

    /**
     * Get maximum price from layer products set
     *
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
     * @return int
     */
    private function getMinRangePower()
    {
        $options = $this->dataProvider->getOptions();

        return $options['min_range_power'];
    }

    /**
     * @param int $range
     * @param array $dbRanges
     * @return array
     */
    private function prepareData($range, array $dbRanges)
    {
        $data = [];
        if (!empty($dbRanges)) {
            $lastIndex = array_keys($dbRanges);
            $lastIndex = $lastIndex[count($lastIndex) - 1];

            foreach ($dbRanges as $index => $count) {
                $fromPrice = $index == 1 ? '' : ($index - 1) * $range;
                $toPrice = $index == $lastIndex ? '' : $index * $range;

                $data[] = [
                    'from' => $fromPrice,
                    'to' => $toPrice,
                    'count' => $count
                ];
            }
        }
        return $data;
    }
}
