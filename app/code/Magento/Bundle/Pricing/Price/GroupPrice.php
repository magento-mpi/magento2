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
 * Bundle droup price model
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
            $basePrice = $this->getBasePrice();
            $this->value = $basePrice - $basePrice * ($groupPrice / 100);
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
            ->getPrice(\Magento\Catalog\Pricing\Price\BasePrice::PRICE_TYPE_CODE, $qty)
            ->getValue();
    }
}
