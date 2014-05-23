<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Pricing\Price;

use Magento\Catalog\Pricing\Price\RegularPrice;

/**
 * Bundle group price model
 */
class GroupPrice extends \Magento\Catalog\Pricing\Price\GroupPrice implements DiscountProviderInterface
{
    /**
     * @var float|false
     */
    protected $percent;

    /**
     * @return bool|float
     */
    public function getDiscountPercent()
    {
        if ($this->percent === null) {
            $this->percent = parent::getValue();
        }
        return $this->percent;
    }

    /**
     * @return float|bool
     */
    public function getValue()
    {
        if ($this->value !== null) {
            return $this->value;
        }

        $groupPrice = $this->getDiscountPercent();
        if ($groupPrice) {
            $regularPrice = $this->getRegularPrice();
            $this->value = $regularPrice * ($groupPrice / 100);
        } else {
            $this->value = false;
        }
        return $this->value;
    }

    /**
     * @return bool|float
     */
    protected function getRegularPrice()
    {
        return $this->priceInfo->getPrice(RegularPrice::PRICE_CODE)->getValue();
    }
}
