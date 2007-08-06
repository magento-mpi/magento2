<?php
/**
 * One page checkout status
 *
 * @package    Mage
 * @subpackage Checkout
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Checkout_Block_Onepage_Status extends Mage_Checkout_Block_Onepage_Abstract
{
    public function getBilling()
    {
        return $this->getQuote()->getBillingAddress();
    }
    
    public function getShipping()
    {
        return $this->getQuote()->getShippingAddress();
    }
    
    public function getShippingDescription()
    {
        return $this->getQuote()->getShippingAddress()->getShippingDescription();
    }
    
    public function getPaymentBlock()
    {
        $payment = $quote->getPayment();
        
        $paymentMethodConfig = Mage::getConfig()->getNode('global/sales/payment/methods/'.$payment->getMethod());
        if (!empty($paymentMethodConfig)) {
            $className = $paymentMethodConfig->getClassName();
            $paymentMethod = new $className();
            $paymentBlock = $paymentMethod->setPayment($payment)->createInfoBlock($this->getData('name').'.payment');
        }
        return $paymentBlock;
    }
}