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

use Magento\Catalog\Pricing\Price\BasePrice;

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
     * @param null|float $qty
     * @return bool|float
     */
    protected function getBasePrice($qty = null)
    {
        return $this->priceInfo
            ->getPrice(BasePrice::PRICE_CODE, $qty)
            ->getValue();
    }
}
