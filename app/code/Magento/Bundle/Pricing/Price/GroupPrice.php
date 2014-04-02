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

use Magento\Customer\Model\Session;

/**
 * Group price model
 */
class GroupPrice extends \Magento\Catalog\Pricing\Price\GroupPrice
{
    /**
     * @return float|bool
     */
    public function getValue()
    {
        if ($this->value !== null) {
            return $this->value;
        }

        $groupPrice = parent::getValue();
        if ($groupPrice) {
            $regularPrice = $this->getRegularPrice();
            $this->value = $regularPrice - $regularPrice * ($groupPrice / 100);
        } else {
            $this->value = false;
        }
        return $this->value;
    }

    /**
     * @param null|float $qty
     * @return bool|float
     */
    protected function getRegularPrice($qty = null)
    {
        return $this->priceInfo
            ->getPrice(\Magento\Catalog\Pricing\Price\RegularPrice::PRICE_TYPE_PRICE_DEFAULT, $qty)
            ->getValue();
    }
}
