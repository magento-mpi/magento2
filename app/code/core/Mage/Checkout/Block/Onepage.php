<?php
/**
 * Onepage checkout block
 *
 * @package    Ecom
 * @subpackage Checkout
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Checkout_Block_Onepage extends Mage_Core_Block_Template
{
    protected $_steps;
    protected $_checkout;
    protected $_quote;
    
    public function __construct() 
    {
        parent::__construct();
        $this->setViewName('Mage_Checkout', 'onepage.phtml');
        
        $this->_checkout = Mage::getSingleton('checkout', 'session');
        $this->_quote = $this->_checkout->getQuote();

        $this->_initSteps();
    }
    
    protected function _initSteps()
    {
        $this->_steps = array();
        
        if (!Mage::getSingleton('customer', 'session')->isLoggedIn()) {
            $this->_steps['method'] = array();
            $this->_steps['method']['label'] = 'Checkout';
            $this->_steps['method']['allow'] = true;
            $this->_createMethodBlock();
        } else {
            $this->_checkout->setAllowBilling(true);
        }
        
        $this->_steps['billing'] = array();
        $this->_steps['billing']['label'] = 'Billing Information';
        $this->_createBillingBlock();

        $this->_steps['payment'] = array();
        $this->_steps['payment']['label'] = 'Payment Information';
        $this->_createPaymentBlock();

        $this->_steps['shipping'] = array();
        $this->_steps['shipping']['label'] = 'Shipping Information';
        $this->_createShippingBlock();

        $this->_steps['shipping_method'] = array();
        $this->_steps['shipping_method']['label'] = 'Shipping Method';
        $this->_createShippingMethodBlock();

        $this->_steps['review'] = array();
        $this->_steps['review']['label'] = 'Order Review';
        $this->_createReviewBlock();
        
        foreach ($this->_steps as $stepId=>$stepInfo) {
            if ($this->_checkout->getData('allow_'.$stepId)) {
                $this->_steps[$stepId]['allow'] = true;
            }
        }
        $this->assign('steps', $this->_steps);
    }

    protected function _createMethodBlock()
    {
        $data = $this->_checkout->getCheckoutMethodData();
        
        $block = Mage::createBlock('tpl', 'checkout.method')
            ->setViewName('Mage_Checkout', 'onepage/method.phtml')
            ->assign('data', $data);
            
        $this->setChild('method', $block);
    }
    
    protected function _createBillingBlock()
    {
        $block = Mage::createBlock('tpl', 'checkout.billing')
            ->setViewName('Mage_Checkout', 'onepage/billing.phtml');
            
        $billing = $this->_quote->getAddressByType('billing');
        if (empty($billing)) {
            $billing = Mage::getModel('sales', 'quote_entity_address');
        }
        
        // assign customer addresses
        $customerSession = Mage::getSingleton('customer', 'session');
        if ($customerSession->isLoggedIn()) {
            $customer = $customerSession->getCustomer();
            $addresses = $customer->getAddressCollection();
            $block->assign('addresses', $addresses->getItems());
        }
        
        $block->assign('address', $billing);
        
        $this->setChild('billing', $block);
    }

    protected function _createPaymentBlock()
    {
        $payment = $this->_quote->getPayment();
        if (empty($payment)) {
            $payment = Mage::getModel('sales', 'quote_entity_payment');
        }
        if ($payment->getCcNumber()) {
            $payment->setCcNumber($payment->decrypt($payment->getCcNumber()));
        }
        
        $paymentBlock = Mage::createBlock('tpl', 'checkout.payment')
            ->setViewName('Mage_Checkout', 'onepage/payment.phtml');
        $listBlock = Mage::createBlock('list', 'checkout.payment.methods');    
        $paymentBlock->setChild('paymentMethods', $listBlock);
        
        $methods = Mage::getConfig()->getGlobalCollection('salesPayment')->children();
        foreach ($methods as $methodConfig) {
            $methodName = $methodConfig->getName();
            $className = $methodConfig->getClassName();
            $method = new $className();
            $method->setPayment($payment);
            $methodBlock = $method->createFormBlock('checkout.payment.methods.'.$methodName);
            if (!empty($methodBlock)) {
                $listBlock->append($methodBlock);
            }
        }
            
        $this->setChild('payment', $paymentBlock);
    }

    protected function _createShippingBlock()
    {
        $block = Mage::createBlock('tpl', 'checkout.shipping')
            ->setViewName('Mage_Checkout', 'onepage/shipping.phtml');
            
        $shipping = $this->_quote->getAddressByType('shipping');
        if (empty($shipping)) {
            $shipping = Mage::getModel('sales', 'quote_entity_address');
        }
        
        // assign customer addresses
        $customerSession = Mage::getSingleton('customer', 'session');
        if ($customerSession->isLoggedIn()) {
            $customer = $customerSession->getCustomer();
            $addresses = $customer->getAddressCollection();
            $block->assign('addresses', $addresses->getItems());
        }

        $block->assign('address', $shipping);
        
        $this->setChild('shipping', $block);
    }

    protected function _createShippingMethodBlock()
    {
        $block = Mage::createBlock('checkout_shipping_method', 'checkout.onepage.shipping_method');

        $this->setChild('shipping_method', $block);
    }

    protected function _createReviewBlock()
    {
        $status = Mage::createBlock('checkout_onepage_status', 'checkout.review.stub');
        
        $block = Mage::createBlock('tpl', 'checkout.review')
            ->setViewName('Mage_Checkout', 'onepage/review.phtml')
            ->setChild('status', $status)
            ->assign('data', $this->_quote);
            
        $this->setChild('review', $block);
    }
    
    public function getLastAllowStep()
    {
        $step = false;
        foreach ($this->_steps as $stepId=>$stepInfo) {
            if(!empty($stepInfo['allow'])){
                $step = $stepId;
            }
        }
        return $step;
    }
}