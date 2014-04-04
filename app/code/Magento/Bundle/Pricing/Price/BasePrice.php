<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
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
            $this->value = parent::getValue();
            $this->value += $this->priceInfo
                ->getPrice(BundleOptionPriceInterface::PRICE_TYPE_BUNDLE_OPTION)
                ->getValue();
            $this->value = $this->applyDiscount($this->value);
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
            $this->priceInfo->getPrice(CatalogPrice\TierPriceInterface::PRICE_TYPE_TIER)->getValue(),
            $this->priceInfo->getPrice(CatalogPrice\GroupPriceInterface::PRICE_TYPE_GROUP)->getValue(),
            $this->priceInfo->getPrice(CatalogPrice\SpecialPriceInterface::PRICE_TYPE_SPECIAL)->getValue()
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
            $this->value = parent::getMaxValue();
            $this->value += $this->priceInfo
                ->getPrice(BundleOptionPriceInterface::PRICE_TYPE_BUNDLE_OPTION)
                ->getMaxValue();
            $this->value = $this->applyDiscount($this->value);
        }
        return $this->maxValue;
    }
}
