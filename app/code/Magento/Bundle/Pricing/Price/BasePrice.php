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
            $discount = [
                0,
                $this->priceInfo->getPrice(CatalogPrice\TierPriceInterface::PRICE_TYPE_TIER)->getValue(),
                $this->priceInfo->getPrice(CatalogPrice\GroupPriceInterface::PRICE_TYPE_GROUP)->getValue(),
                $this->priceInfo->getPrice(CatalogPrice\SpecialPriceInterface::PRICE_TYPE_SPECIAL)->getValue()
            ];
            $discount = max($discount);
            if ($discount) {
                $this->value = $this->value - $this->value * ($discount / 100);
            }
        }
        return $this->value;
    }

    /**
     * Get Max Value
     *
     * @return bool|float
     */
    public function getMaxValue()
    {
        if ($this->maxValue === null) {
            $this->value = false;
            foreach ($this->priceInfo->getPricesIncludedInBase() as $price) {
                $this->maxValue = max($price->getValue(), $this->maxValue ?: $price->getValue());
            }
        }
        return $this->maxValue;
    }
}
