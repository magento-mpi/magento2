<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Pricing\Price;

use Magento\Catalog\Pricing\Price as CatalogPrice;

/**
 * Bundle Base Price model
 */
class BasePrice extends CatalogPrice\BasePrice
{
    /**
     * Get Base Price Value
     *
     * @return float|bool
     */
    public function getValue()
    {
        if ($this->value === null) {
            $this->value = $this->applyDiscount(parent::getValue());
        }
        return $this->value;
    }

    /**
     * Apply discount type prices
     *
     * @param float $price
     * @return float
     */
    public function applyDiscount($price)
    {
        $discount = [
            0,
            $this->priceInfo
                ->getPrice(CatalogPrice\TierPriceInterface::PRICE_TYPE_TIER, $this->quantity)
                ->getValue(),
            $this->priceInfo
                ->getPrice(CatalogPrice\GroupPriceInterface::PRICE_TYPE_GROUP, $this->quantity)
                ->getValue(),
            $this->priceInfo
                ->getPrice(CatalogPrice\SpecialPriceInterface::PRICE_TYPE_SPECIAL, $this->quantity)
                ->getValue()
        ];
        $discount = max($discount);
        if ($discount) {
            $price = $price - $price * ($discount / 100);
        }
        return $price;
    }

    /**
     * Get Max Value
     *
     * @return bool|float
     */
    public function getMaxValue()
    {
        if ($this->maxValue === null) {
            $this->value = $this->applyDiscount(parent::getMaxValue());
        }
        return $this->maxValue;
    }
}
