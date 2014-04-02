<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Pricing\Price;

/**
 * Class BasePrice
 */
class BasePrice extends RegularPrice
{
    /**
     * Price type identifier string
     */
    const PRICE_TYPE_BASE_PRICE = 'base_price';

    /**
     * @var string
     */
    protected $priceType = self::PRICE_TYPE_BASE_PRICE;

    /**
     * @var bool|float|null
     */
    protected $maxValue;

    /**
     * Get Base Price Value
     *
     * @return float|bool
     */
    public function getValue()
    {
        if ($this->value === null) {
            $this->value = false;
            foreach ($this->priceInfo->getPricesIncludedInBase() as $price) {
                $this->value = min($price->getValue(), $this->value ?: $price->getValue());
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
