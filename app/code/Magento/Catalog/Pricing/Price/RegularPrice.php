<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Pricing\Price;

/**
 * Class RegularPrice
 */
class RegularPrice extends AbstractPrice
{
    /**
     * Price type
     */
    const PRICE_CODE = 'regular_price';

    /**
     * Get price value
     *
     * @return float|bool
     */
    public function getValue()
    {
        if ($this->value === null) {
            $price = $this->product->getPrice();
            $this->value = $price ? floatval($price) : false;
        }
        return $this->value;
    }
}
