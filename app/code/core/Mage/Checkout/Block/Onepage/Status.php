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
        
        $billingEntity = $quote->getAddressByType('billing');
        if (empty($billingEntity)) {
            $billingEntity = Mage::getModel('sales', 'quote_entity')->setEntityType('address');
        }
        $billing = $billingEntity->asArray();
        
        $paymentEntity = $quote->getPayment('payment');
        if (empty($paymentEntity)) {
            $paymentEntity = Mage::getModel('sales', 'quote_entity')->setEntityType('payment');
        }
        $payment = $paymentEntity->asModel('customer', 'payment');
        if ($payment) {
            $payment->setCcNumber($payment->decrypt($payment->getCcNumber()));
            $className = Mage::getConfig()->getGlobalCollection('salesPayment', $payment->getMethod())->getClassName();
            $paymentModel = new $className();
            $paymentBlock = $paymentModel->setPayment($payment)->createInfoBlock($this->getInfo('name').'.payment');
            $this->setChild('payment', $paymentBlock);
        } else {
            $this->assign('payment', '');
        }
                
        $shippingEntity = $quote->getAddressByType('shipping');
        if (empty($shippingEntity)) {
            $shippingEntity = Mage::getModel('sales', 'quote_entity')->setEntityType('address');
        }
        $shipping = $shippingEntity->asArray();
        
        $shippingMethod = array();
        
        $this->assign('checkout', $checkout)->assign('quote', $quote)
            ->assign('billing', $billing)
            ->assign('shipping', $shipping)
            ->assign('shippingMethod', $shippingMethod);
    }
}