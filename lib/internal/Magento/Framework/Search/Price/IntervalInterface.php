<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Price;

interface IntervalInterface
{
    /**
     * @param int $limit
     * @param null|int $offset
     * @param null|int $lowerPrice
     * @param null|int $upperPrice
     * @return array
     */
    public function load($limit, $offset = null, $lowerPrice = null, $upperPrice = null);

    /**
     * @param float $price
     * @param int $index
     * @param null|int $lowerPrice
     * @return array
     */
    public function loadPrevious($price, $index, $lowerPrice = null);

    /**
     * @param float $price
     * @param int $rightIndex
     * @param null|int $upperPrice
     * @return array
     */
    public function loadNext($price, $rightIndex, $upperPrice = null);
}
