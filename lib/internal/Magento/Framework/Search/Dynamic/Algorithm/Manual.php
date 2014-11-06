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
        $range = $dataProvider->getRange();
        $options = $dataProvider->getOptions();
        if (!$range) {
            $range = $options['range_step'];
        }
        $dbRanges = $this->dataProvider->getAggregation($bucket, $dimensions, $range, $entityIds);
        $dbRanges = $this->processRange($dbRanges, $options['max_intervals_number']);
        $data = $dataProvider->prepareData($range, $dbRanges);

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
}
