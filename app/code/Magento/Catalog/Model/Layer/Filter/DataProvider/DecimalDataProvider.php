<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Layer\Filter\DataProvider;

use Magento\Catalog\Model\Layer\Filter\AbstractFilter;

class DecimalDataProvider
{
    const MIN_RANGE_POWER = 10;

    /**
     * @var int
     */
    private $max;

    /**
     * @var int
     */
    private $min;

    /**
     * @var int
     */
    private $range;

    /**
     * @var array
     */
    private $rangeItemsCount = [];

    /**
     * @var \Magento\Catalog\Model\Resource\Layer\Filter\Decimal
     */
    private $resource;

    /**
     * @param \Magento\Catalog\Model\Layer $layer
     * @param \Magento\Catalog\Model\Resource\Layer\Filter\Decimal $resource
     */
    public function __construct(
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Resource\Layer\Filter\Decimal $resource
    ) {

        $this->resource = $resource;
    }

    /**
     * @param AbstractFilter $filter
     * @return int
     */
    public function getRange(AbstractFilter $filter)
    {
        $range = $this->range;
        if (!$range) {
            $maxValue = $this->getMaxValue($filter);
            $index = 1;
            do {
                $range = pow(10, strlen(floor($maxValue)) - $index);
                $items = $this->getRangeItemCounts($range, $filter);
                $index++;
            } while ($range > self::MIN_RANGE_POWER && count($items) < 2);
            $this->range = $range;
        }

        return $range;
    }

    /**
     * Retrieve maximum value from layer products set
     *
     * @param AbstractFilter $filter
     * @return float
     */
    public function getMaxValue(AbstractFilter $filter)
    {
        $max = $this->max;
        if (is_null($max)) {
            list($min, $max) = $this->getResource()->getMinMax($filter);
            $this->max = $max;
            $this->min = $min;
        }
        return $max;
    }

    /**
     * Retrieve minimal value from layer products set
     *
     * @param AbstractFilter $filter
     * @return float
     */
    public function getMinValue(AbstractFilter $filter)
    {
        $min = $this->min;
        if (is_null($min)) {
            list($min, $max) = $this->getResource()->getMinMax($filter);
            $this->max = $max;
            $this->min = $min;
        }
        return $min;
    }

    /**
     * Retrieve information about products count in range
     *
     * @param int $range
     * @param AbstractFilter $filter
     * @return int
     */
    public function getRangeItemCounts($range, AbstractFilter $filter)
    {
        $count = array_key_exists($range, $this->rangeItemsCount) ? $this->rangeItemsCount[$range] : null;
        if (is_null($count)) {
            $count = $this->getResource()->getCount($filter, $range);
            $this->rangeItemsCount[$range] = $count;
        }
        return $count;
    }

    /**
     * @return \Magento\Catalog\Model\Resource\Layer\Filter\Decimal
     */
    private function getResource()
    {
        return $this->resource;
    }
} 
