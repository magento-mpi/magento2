<?php
/**
 * Checkout front
 *
 * @package    Ecom
 * @subpackage Checkout
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Checkout_Front
{
    
    public static function unsetAll()
    {
        Mage::getSingleton('checkout_model', 'session')->unsetAll();
    }
    
    public static function loadCustomerQuote()
    {
        Mage::getSingleton('checkout_model', 'session')->loadCustomerQuote();
    }

}