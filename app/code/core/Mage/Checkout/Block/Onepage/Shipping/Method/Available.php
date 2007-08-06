<?php
/**
 * One page checkout status
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @subpackage Onepage
 * @author     Moshe Gurvich <moshe@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Checkout_Block_Onepage_Shipping_Method_Available extends Mage_Checkout_Block_Onepage_Abstract 
{
    public function fetchEnabledMethods()
    {
        $address = $this->getQuote()->getShippingAddress();

        $rates = $address->getAllShippingRates(); 
        if (!empty($rates)) {
            $estimateFilter = new Varien_Filter_Object_Grid();
            $estimateFilter->addFilter(new Varien_Filter_Sprintf('$%s', 2), 'amount');
            $methods = $estimateFilter->filter($rates);
            $selectedMethod = $quote->getShippingMethod();
            $this->assign('methods', $methods)->assign('selectedMethod', $selectedMethod);
        } else {
            $this->assign('methods', array())->assign('selectedMethod', '');
        }

    }
}