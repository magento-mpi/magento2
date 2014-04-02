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
            $basePrice = $this->getBasePrice();
            $this->value = $basePrice - $basePrice * ($tierPrice / 100);
        } else {
            $this->value = false;
        }
        return $this->value;
    }

    /**
     * @param null|float $qty
     * @return bool|float
     */
    protected function getBasePrice($qty = null)
    {
        return $this->priceInfo
            ->getPrice(\Magento\Catalog\Pricing\Price\BasePrice::PRICE_TYPE_BASE_PRICE, $qty)
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
