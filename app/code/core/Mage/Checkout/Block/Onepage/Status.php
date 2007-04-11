<?php
/**
 * One page checkout status
 *
 * @package    Ecom
 * @subpackage Checkout
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Checkout_Block_Onepage_Status extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setViewName('Mage_Checkout', 'onepage/status.phtml');
        
        $checkout = Mage::getSingleton('checkout_model', 'session');
        $quote = $checkout->getQuote();
        
        $billing = $quote->getAddressByType('billing');
        
        $payments = $quote->getEntitiesByType('payment');
        if (!empty($payments)) {
            $payment = $payments[0];
        } else {
            $payment = false;
        }
        
        $shipping = $quote->getAddressByType('shipping');
        
        $shippingMethod = array();
        
        $this->assign('billing', $billing)->assign('payment', $payment)
            ->assign('shipping', $shipping)->assign('shipping_method', $shippingMethod);
    }
}