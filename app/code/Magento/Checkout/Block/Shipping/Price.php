<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Block\Shipping;

use Magento\Sales\Model\Quote\Address\Rate;
use Magento\Checkout\Block\Cart\AbstractCart;

class Price extends AbstractCart
{
    /**
     * @var Rate
     */
    protected $shippingRate;

    /**
     * Set the shipping rate
     *
     * @param Rate $shippingRate
     * @return $this
     */
    public function setShippingRate(Rate $shippingRate)
    {
        $this->shippingRate = $shippingRate;
        return $this;
    }

    /**
     * Return shipping rate
     *
     * @return Rate
     */
    public function getShippingRate()
    {
        return $this->shippingRate;
    }

    /**
     * Get Shipping Price
     *
     * @return float
     */
    public function getShippingPrice()
    {
        $price = $this->shippingRate->getPrice();
        return $this->getQuote()->getStore()->convertPrice($price, true);
    }
}
