<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Helper;

use Magento\Sales\Model\Quote\Item\AbstractItem;
/**
 * SalesRule data helper
 */
class Data extends \Magento\App\Helper\AbstractHelper
{
    /**
     * Set store and base price which will be used during discount calculation to item object
     *
     * @param AbstractItem $item
     * @param float $basePrice
     * @param float $price
     * @return $this
     */
    public function setItemDiscountPrices(AbstractItem $item, $basePrice, $price)
    {
        $item->setDiscountCalculationPrice($price);
        $item->setBaseDiscountCalculationPrice($basePrice);
        return $this;
    }

    /**
     * Add additional amounts to discount calculation prices
     *
     * @param AbstractItem $item
     * @param float $basePrice
     * @param float $price
     * @return $this
     */
    public function addItemDiscountPrices(AbstractItem $item, $basePrice, $price)
    {
        $discountPrice      = $item->getDiscountCalculationPrice();
        $baseDiscountPrice  = $item->getBaseDiscountCalculationPrice();

        if ($discountPrice || $baseDiscountPrice || $basePrice || $price) {
            $discountPrice      = $discountPrice ? $discountPrice : $item->getCalculationPrice();
            $baseDiscountPrice  = $baseDiscountPrice ? $baseDiscountPrice : $item->getBaseCalculationPrice();
            $this->setItemDiscountPrices($item, $baseDiscountPrice+$basePrice, $discountPrice+$price);
        }
        return $this;
    }
}
