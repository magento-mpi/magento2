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
    protected $_rates;
    
    public function getRates()
    {
        if (empty($this->_rates)) {
            $rates = $this->getQuote()->getShippingAddress()->getAllShippingRates();
            if (!empty($rates)) {
                $ratesFilter = new Varien_Filter_Object_Grid();
                $ratesFilter->addFilter(new Varien_Filter_Sprintf('$%s', 2), 'price');
                $this->_rates = $ratesFilter->filter($rates);
            }
        }
        return $this->_rates;
    }
    
    public function getSelectedMethod()
    {
        return $this->getQuote()->getShippingAddress()->getShippingMethod();
    }
}