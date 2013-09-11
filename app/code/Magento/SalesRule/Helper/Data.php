<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * SalesRule data helper
 */
namespace Magento\SalesRule\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
{
    /**
     * Set store and base price which will be used during discount calculation to item object
     *
     * @param   \Magento\Sales\Model\Quote\Item\AbstractItem $item
     * @param   float $basePrice
     * @param   float $price
     * @return  \Magento\SalesRule\Helper\Data
     */
    public function setItemDiscountPrices(\Magento\Sales\Model\Quote\Item\AbstractItem $item, $basePrice, $price)
    {
        $item->setDiscountCalculationPrice($price);
        $item->setBaseDiscountCalculationPrice($basePrice);
        return $this;
    }

    /**
     * Add additional amounts to discount calculation prices
     *
     * @param   \Magento\Sales\Model\Quote\Item\AbstractItem $item
     * @param   float $basePrice
     * @param   float $price
     * @return  \Magento\SalesRule\Helper\Data
     */
    public function addItemDiscountPrices(\Magento\Sales\Model\Quote\Item\AbstractItem $item, $basePrice, $price)
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
