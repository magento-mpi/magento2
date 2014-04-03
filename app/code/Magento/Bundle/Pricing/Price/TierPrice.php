<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Pricing\Price;

/**
 * Bundle tire prices model
 */
class TierPrice extends \Magento\Catalog\Pricing\Price\TierPrice
{
    /**
     * Returns true if first price is better
     *
     * Method filters tiers price values, higher discount value is better
     *
     * @param float $firstPrice
     * @param float $secondPrice
     * @return bool
     */
    protected function isFirstPriceBetter($firstPrice, $secondPrice)
    {
        return $firstPrice > $secondPrice;
    }

    /**
     * Bundle tier prices provide a percent discount and cannot be filtered by base price
     *
     * @param array $priceList
     * @return array
     */
    protected function filterByBasePrice($priceList)
    {
        return $priceList;
    }
}
