<?php
/**
 * Checkout front
 *
 * @package    Mage
 * @subpackage Checkout
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Checkout_Front
{
    
    public static function unsetAll()
    {
        Mage::getSingleton('checkout', 'session')->unsetAll();
    }
    
    public static function loadCustomerQuote()
    {
        Mage::getSingleton('checkout', 'session')->loadCustomerQuote();
    }
}