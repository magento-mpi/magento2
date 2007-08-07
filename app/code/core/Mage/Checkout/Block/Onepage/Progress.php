<?php
/**
 * One page checkout status
 *
 * @package    Mage
 * @subpackage Checkout
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Checkout_Block_Onepage_Progress extends Mage_Checkout_Block_Onepage_Abstract
{
    protected function _construct()
    {
        $this->setTemplate('checkout/onepage/progress.phtml');
        parent::_construct();
    }
    
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