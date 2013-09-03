<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Checkout_Block_Cart_Coupon extends Magento_Checkout_Block_Cart_Abstract
{
    public function getCouponCode()
    {
        return $this->getQuote()->getCouponCode();
    }


}
