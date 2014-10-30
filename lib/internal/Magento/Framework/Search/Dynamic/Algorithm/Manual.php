<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Dynamic\Algorithm;

class Manual implements AlgorithmInterface
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
                $options = $this->dataProvider->getOptions();
                $range = $options['range_step'];
                $dbRanges = $this->dataProvider->getCount($range, $entityIds);
                $dbRanges = $this->processRange($dbRanges, $options['max_intervals_number']);
                $data = $this->dataProvider->prepareData($range, $dbRanges);
            }
        }

        return $data;
    }

    /**
     * @param array $items
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
 