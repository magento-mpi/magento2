<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Checkout\Block\Cart;

class Coupon extends \Magento\Checkout\Block\Cart\AbstractCart
{
    public function getCouponCode()
    {
        return $this->getQuote()->getCouponCode();
    }


}
