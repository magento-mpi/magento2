<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Dynamic\Algorithm;

use Magento\Framework\Search\Request\BucketInterface;

class Manual extends AbstractAlgorithm
{
    /**
     * {@inheritdoc}
     */
    public function getItems(BucketInterface $bucket, array $dimensions, array $entityIds)
    {
        $range = $this->dataProvider->getRange();
        $options = $this->dataProvider->getOptions();
        if (!$range) {
            $range = $options['range_step'];
        }
        $dbRanges = $this->dataProvider->getAggregation($bucket, $dimensions, $range, $entityIds);
        $dbRanges = $this->processRange($dbRanges, $options['max_intervals_number']);
        $data = $this->prepareData($range, $dbRanges);

        return $data;
    }

    /**
     * @param array $items \
     * @param int $maxIntervalsNumber
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

    /**
     * @param int $range
     * @param int[] $dbRanges
     * @return array
     */
    public function prepareData($range, array $dbRanges)
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
