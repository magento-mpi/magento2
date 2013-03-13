<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Checkout_Block_Cart_Coupon extends Mage_Checkout_Block_Cart_Abstract
{
    public function getCouponCode()
    {
        return $this->getQuote()->getCouponCode();
    }


}
