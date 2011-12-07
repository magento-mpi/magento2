<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * One page checkout status
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Checkout_Block_Onepage_Progress extends Mage_Checkout_Block_Onepage_Abstract
{
    public function getBilling()
    {
        return $this->getQuote()->getBillingAddress();
    }

    public function getShipping()
    {
        return $this->getQuote()->getShippingAddress();
    }

    public function getShippingMethod()
    {
        return $this->getQuote()->getShippingAddress()->getShippingMethod();
    }

    public function getShippingDescription()
    {
        return $this->getQuote()->getShippingAddress()->getShippingDescription();
    }

    public function getShippingAmount()
    {
        /*$amount = $this->getQuote()->getShippingAddress()->getShippingAmount();
        $filter = Mage::app()->getStore()->getPriceFilter();
        return $filter->filter($amount);*/
        //return $this->helper('Mage_Checkout_Helper_Data')->formatPrice(
        //    $this->getQuote()->getShippingAddress()->getShippingAmount()
        //);
        return $this->getQuote()->getShippingAddress()->getShippingAmount();
    }

    public function getPaymentHtml()
    {
        return $this->getChildHtml('payment_info');
    }

    /**
     * Get quote shipping price including tax
     * @return float
     */
    public function getShippingPriceInclTax()
    {
        $inclTax = $this->getQuote()->getShippingAddress()->getShippingInclTax();
        return $this->formatPrice($inclTax);
    }

    public function getShippingPriceExclTax()
    {
        return $this->formatPrice($this->getQuote()->getShippingAddress()->getShippingAmount());
    }

    public function formatPrice($price)
    {
        return $this->getQuote()->getStore()->formatPrice($price);
    }
}
