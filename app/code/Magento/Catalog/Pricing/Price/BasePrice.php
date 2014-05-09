<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Pricing\Price;

use Magento\Framework\Pricing\Price\AbstractPrice;

/**
 * Class BasePrice
 */
class BasePrice extends AbstractPrice
{
    /**
     * Price type identifier string
     */
    const PRICE_CODE = 'base_price';

    /**
     * Get Base Price Value
     *
     * @return float|bool
     */
    public function getValue()
    {
        if ($this->value === null) {
            $this->value = false;
            foreach ($this->priceInfo->getPrices() as $code => $price) {
                if ($price instanceof \Magento\Framework\Pricing\Price\BasePriceProviderInterface && $price->getValue()
                ) {
                    $this->value = min($price->getValue(), $this->value ? : $price->getValue());
                }
            }
        }
        return $this->value;
    }
}
