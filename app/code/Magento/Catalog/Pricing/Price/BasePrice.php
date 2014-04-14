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
class BasePrice extends AbstractPrice
{
    /**
     * Price type identifier string
     */
    const PRICE_TYPE_CODE = 'base_price';

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
}
