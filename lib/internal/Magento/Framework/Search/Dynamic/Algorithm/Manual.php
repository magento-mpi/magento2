<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Dynamic\Algorithm;

use Magento\Framework\Search\Adapter\Mysql\Aggregation\DataProviderInterface;

class Manual implements AlgorithmInterface
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
                $options = $dataProvider->getOptions();
                $range = $options['range_step'];
                $dbRanges = $dataProvider->getAggregation($range, $entityIds, 'count');
                $dbRanges = $this->processRange($dbRanges, $options['max_intervals_number']);
                $data = $dataProvider->prepareData($range, $dbRanges);
            }
        }

        return $data;
    }

    /**
     * @param array $items\
     * @param $maxIntervalsNumber
     * @return array
     */
    private function processRange($items, $maxIntervalsNumber)
    {
        $i = 0;
        $lastIndex = null;
        foreach ($items as $k => $v) {
            ++$i;
            if ($i > 1 && $i > $maxIntervalsNumber) {
                $items[$lastIndex] += $v;
                unset($items[$k]);
            } else {
                $lastIndex = $k;
            }
        }
        return $items;
    }
}
 