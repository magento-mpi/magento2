<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Pricing\Price;

use Magento\Bundle\Model\Product\Price;

/**
 * Bundle regular price model
 */
class RegularPrice extends \Magento\Catalog\Pricing\Price\RegularPrice
{
    /**
     * Get price value
     *
     * @return float|bool
     */
    public function getValue()
    {
        if ($this->value !== null) {
            return $this->value;
        }
        if ($this->salableItem->getPriceType() == Price::PRICE_TYPE_FIXED) {
            $this->value = parent::getValue();
        } else {
            $this->value = false;
        }
        return $this->value;
    }
}
