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
        
        $checkout = Mage::getSingleton('checkout', 'session');
        $quote = $checkout->getQuote();
        
        $billing = $quote->getAddressByType('billing');
        if (empty($billing)) {
            $billing = Mage::getModel('sales', 'quote_entity_address');
        }
        
        $payment = $quote->getPayment('payment');
        if (empty($payment)) {
            $payment = Mage::getModel('sales', 'quote_entity_payment');
        }
        if ($payment) {
            if ($payment->getCcNumber()) {
                $payment->setCcNumber($payment->decrypt($payment->getCcNumber()));
            }
            $paymentMethodConfig = Mage::getConfig()->getGlobalCollection('salesPayment', $payment->getMethod());
            if (!empty($paymentMethodConfig)) {
                $className = $paymentMethodConfig->getClassName();
                $paymentMethod = new $className();
                $paymentBlock = $paymentMethod->setPayment($payment)->createInfoBlock($this->getInfo('name').'.payment');
                $this->setChild('payment', $paymentBlock);
            } else {
                $this->assign('payment', '');
            }
        } else {
            $this->assign('payment', '');
        }
                
        $shipping = $quote->getAddressByType('shipping');
        if (empty($shipping)) {
            $shipping = Mage::getModel('sales', 'quote_entity_address');
        }
       
        $shippingMethod = array();
        
        $this->assign('checkout', $checkout)->assign('quote', $quote)
            ->assign('billing', $billing)
            ->assign('shipping', $shipping)
            ->assign('shippingMethod', $shippingMethod);
    }
}