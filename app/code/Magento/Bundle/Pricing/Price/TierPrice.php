<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Pricing\Price;

use Magento\Pricing\Adjustment\Calculator;
use Magento\Pricing\Object\SaleableInterface;
use Magento\Customer\Model\Group;
use Magento\Customer\Model\Session;

/**
 * Tire prices model
 */
class TierPrice extends \Magento\Catalog\Pricing\Price\TierPrice
{

    /**
     * Get price value
     *
     * @return bool|float
     */
    public function getValue()
    {
        if ($this->value !== null) {
            return $this->value;
        }

        $tierPrice = parent::getValue();

        if ($tierPrice) {
            $regularPrice = $this->getRegularPrice();
            $this->value = $regularPrice - $regularPrice * ($tierPrice / 100);
        } else {
            $this->value = false;
        }
        return $this->value;
    }

    /**
     * @param null|float $qty
     * @return bool|float
     */
    protected function getRegularPrice($qty = null)
    {
        return $this->priceInfo
            ->getPrice(\Magento\Catalog\Pricing\Price\RegularPrice::PRICE_TYPE_PRICE_DEFAULT, $qty)
            ->getValue();
    }

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
