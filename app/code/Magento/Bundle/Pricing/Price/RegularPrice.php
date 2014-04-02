<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Pricing\Price;

use Magento\Pricing\Adjustment\Calculator;
use Magento\Pricing\Amount\AmountInterface;
use Magento\Pricing\PriceInfoInterface;
use Magento\Pricing\Object\SaleableInterface;

/**
 * Class RegularPrice
 */
class RegularPrice extends \Magento\Catalog\Pricing\Price\RegularPrice
{
    /**
     * Get price value
     *
     * @throws \Exception
     * @return float|bool
     */
    public function getValue()
    {
        if ($this->value !== null) {
            return $this->value;
        }
        if ($this->salableItem->getPriceType() == \Magento\Bundle\Model\Product\Price::PRICE_TYPE_FIXED) {
            $this->value = parent::getValue();
        } else {
            $this->value = 0;
            throw new \Exception('Define logic for dynamic type');
        }
        return $this->value;
    }
}
