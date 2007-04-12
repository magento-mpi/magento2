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
        
        $this->_checkout = Mage::getSingleton('checkout_model', 'session');
        $this->_quote = $this->_checkout->getQuote();

        $this->_initSteps();
    }
    
    protected function _initSteps()
    {
        $this->_steps = array();
        
        if (!Mage::getSingleton('customer_model', 'session')->isLoggedIn()) {
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
            
        $billingEntity = $this->_quote->getAddressByType('billing');
        if (empty($billingEntity)) {
            $billingEntity = Mage::getModel('sales', 'quote_entity')->setEntityType('address');
        }
        $billing = $billingEntity->asModel('customer', 'address');
        
        // assign customer addresses
        $customerSession = Mage::getSingleton('customer_model', 'session');
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
        
        $block = Mage::createBlock('tpl', 'checkout.payment')
            ->setViewName('Mage_Checkout', 'onepage/payment.phtml')
            ->assign('payment', $payment);
            
        $this->setChild('payment', $block);
    }

    protected function _createShippingBlock()
    {
        $block = Mage::createBlock('tpl', 'checkout.shipping')
            ->setViewName('Mage_Checkout', 'onepage/shipping.phtml');
            
        $shippingEntity = $this->_quote->getAddressByType('shipping');
        if (empty($shippingEntity)) {
            $shippingEntity = Mage::getModel('sales', 'quote_entity')->setEntityType('address');
        }
        $shipping = $shippingEntity->asModel('customer', 'address');
        
        // assign customer addresses
        $customerSession = Mage::getSingleton('customer_model', 'session');
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
        $quotes = $this->_checkout->getShippingMethods('shipping_method', 'quotes');
        $data = $this->_checkout->getStateData('shipping_method', 'data');

        $block = Mage::createBlock('tpl', 'checkout.shipping_method')
            ->setViewName('Mage_Checkout', 'onepage/shipping_method.phtml')
            ->assign('quotes', $quotes)
            ->assign('data', $data);

        $this->setChild('shipping_method', $block);
    }

    protected function _createReviewBlock()
    {
        $block = Mage::createBlock('tpl', 'checkout.review')
            ->setViewName('Mage_Checkout', 'onepage/review.phtml')
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