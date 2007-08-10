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
    
    public function getShippingAmount()
    {
        $amount = $this->getQuote()->getShippingAddress()->getShippingAmount();
        $filter = new Varien_Filter_Sprintf('$%s', 2);
        return $filter->filter($amount);
    }
    
    public function getPaymentHtml()
    {
        $payment = $this->getQuote()->getPayment();
        
        $html = '<p>'.Mage::getStoreConfig('payment/'.$payment->getMethod().'/title').'</p>';

        $model = Mage::getStoreConfig('payment/'.$payment->getMethod().'/model');
        $block = Mage::getModel($model)
            ->setPayment($payment)
            ->createInfoBlock($this->getData('name').'.payment');
        
        $html.= $block->toHtml();
        
        return $html;
    }
}