<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Pricing\Price;

use Magento\Catalog\Pricing\Price\RegularPrice;

/**
 * Bundle tire prices model
 */
class TierPrice extends \Magento\Catalog\Pricing\Price\TierPrice implements DiscountProviderInterface
{
    /**
     * @var bool
     */
    protected $filterByBasePrice = false;

    /**
     * @var float|false
     */
    protected $percent;

    /**
     * @return bool|float
     */
    public function getDiscountPercent()
    {
        if ($this->percent === null) {
            $this->percent = parent::getValue();
        }
        return $this->percent;
    }

    /**
     * @return bool|float
     */
    public function getValue()
    {
        if ($this->value !== null) {
            return $this->value;
        }

        $tierPrice = $this->getDiscountPercent();
        if ($tierPrice) {
            $regularPrice = $this->getRegularPrice();
            $this->value = $regularPrice - $regularPrice * ($tierPrice / 100);
        } else {
            $this->value = false;
        }
        return $this->value;
    }

    /**
     * @return bool|float
     */
    protected function getRegularPrice()
    {
        return $this->priceInfo->getPrice(RegularPrice::PRICE_CODE)->getValue();
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
}
