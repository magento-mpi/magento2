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
class BasePrice extends CatalogPrice\BasePrice implements BasePriceInterface
{
    /**
     * Get Base Price Value
     *
     * @return float|bool
     */
    public function getValue()
    {
        if ($this->value === null) {
            $this->value = $this->calculateBaseValue(parent::getValue());
        }
        return $this->value;
    }

    /**
     * Calculate base price for passed regular one
     *
     * @param float $price
     * @return float
     */
    public function calculateBaseValue($price)
    {
        $discount = [
            0,
            $this->priceInfo->getPrice(CatalogPrice\TierPrice::PRICE_CODE, $this->quantity)->getValue(),
            $this->priceInfo->getPrice(CatalogPrice\GroupPrice::PRICE_CODE, $this->quantity)->getValue(),
            $this->priceInfo->getPrice(CatalogPrice\SpecialPrice::PRICE_CODE, $this->quantity)->getValue()
        ];
        $discount = max($discount);
        if ($discount) {
            $price = $price - $price * ($discount / 100);
        }
        return $price;
    }
}
