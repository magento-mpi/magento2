<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Pricing\Price;

use Magento\Catalog\Pricing\Price\BasePrice as CatalogBasePrice;

/**
 * Special price model
 */
class SpecialPrice extends \Magento\Catalog\Pricing\Price\SpecialPrice
{
    /**
     * Price type special
     */
    const PRICE_CODE = 'special_price';

    /**
     * @return bool|float
     */
    public function getValue()
    {
        if ($this->value !== null) {
            return $this->value;
        }

        $specialPrice = parent::getValue();
        if ($specialPrice) {
            $basePrice = $this->getBasePrice();
            $this->value = $basePrice - $basePrice * ($specialPrice / 100);
        } else {
            $this->value = false;
        }
        return $this->value;
    }

    /**
     * @return bool|float
     */
    protected function getBasePrice()
    {
        return $this->priceInfo
            ->getPrice(CatalogBasePrice::PRICE_CODE)
            ->getValue();
    }
}
