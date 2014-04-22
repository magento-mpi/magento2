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
                if ($price instanceof \Magento\Pricing\Price\BasePriceProviderInterface && $price->getValue()) {
                    $this->value = min($price->getValue(), $this->value ?: $price->getValue());
                }
            }
        }
        return $this->value;
    }
}
