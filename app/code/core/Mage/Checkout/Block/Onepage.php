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
        $this->setTemplate('checkout/onepage.phtml');
        
        $this->_checkout = Mage::getSingleton('checkout', 'session');
        $this->_quote = $this->_checkout->getQuote();
    }
    
    public function init()
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
        
        return $this;
    }

    protected function _createMethodBlock()
    {
        $data = $this->_checkout->getCheckoutMethodData();
        
        $block = $this->getLayout()->createBlock('tpl', 'checkout.method')
            ->setTemplate('checkout/onepage/method.phtml')
            ->assign('messages', Mage::getSingleton('customer', 'session')->getMessages(true))
            ->assign('postAction', Mage::getUrl('customer', array('controller'=>'account', 'action'=>'loginPost', '_secure'=>true)))
            ->assign('method', $this->_quote->getMethod())
            ->assign('data', $data);
            
        $this->setChild('method', $block);
    }
    
    protected function _createBillingBlock()
    {
        $block = $this->getLayout()->createBlock('tpl', 'checkout.billing')
            ->setTemplate('checkout/onepage/billing.phtml');
            
        $billing = $this->_quote->getAddressByType('billing');
        if (empty($billing)) {
            $billing = Mage::getModel('sales', 'quote_entity_address');
        }
        
        $shipping = $this->_quote->getAddressByType('shipping');
        if ($shipping instanceof Varien_Data_Object) {
            $useForShipping = $shipping->getSameAsBilling();
        }
        else {
            $useForShipping = null;
        }
        
        
        // assign customer addresses
        $customerSession = Mage::getSingleton('customer', 'session');
        if ($customerSession->isLoggedIn()) {
            $customer = $customerSession->getCustomer();
            $addresses = $customer->getAddressCollection();
            $block->assign('addresses', $addresses->getItems());
        }
        
        $countries = Mage::getModel('directory', 'country_collection');
        $block->assign('address', $billing)
            ->assign('useForShipping', $useForShipping)
            ->assign('isCustomerLoggedIn',    Mage::getSingleton('customer', 'session')->isLoggedIn())
            ->assign('countries',   $countries->loadByCurrentDomain())
            ->assign('method', $this->_quote->getMethod())
            ->assign('regions',     $countries->getDefault($billing->getCountryId())->getRegions());
        
        $this->setChild('billing', $block);
    }

    protected function _createPaymentBlock()
    {
        $payment = $this->_quote->getPayment();
        if (empty($payment)) {
            $payment = Mage::getModel('sales', 'quote_entity_payment');
        }
        $payment->setCcNumber(null)->setCcCid(null);
        
        $paymentBlock = $this->getLayout()->createBlock('tpl', 'checkout.payment')
            ->setTemplate('checkout/onepage/payment.phtml')
            ->assign('payment', $payment);
        $listBlock = $this->getLayout()->createBlock('list', 'checkout.payment.methods');    
        $paymentBlock->setChild('paymentMethods', $listBlock);
        
        $methods = Mage::getConfig()->getNode('global/salesPaymentMethods')->children();
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
        $block = $this->getLayout()->createBlock('tpl', 'checkout.shipping')
            ->setTemplate('checkout/onepage/shipping.phtml');
            
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

        $countries = Mage::getModel('directory', 'country_collection');
        $block->assign('address', $shipping)
            ->assign('countries',   $countries->loadByCurrentDomain())
            ->assign('regions',     $countries->getDefault($shipping->getCountryId())->getRegions());
        
        $this->setChild('shipping', $block);
    }

    protected function _createShippingMethodBlock()
    {
        $availableMethods = $this->getLayout()->createBlock('checkout_shipping_method', 'checkout.onepage.shipping_method.available');

        $block = $this->getLayout()->createBlock('tpl', 'checkout.onepage.shipping_method')
            ->setTemplate('checkout/onepage/shipping_method.phtml')
            ->setChild('availableMethods', $availableMethods);

        $this->setChild('shipping_method', $block);
    }

    protected function _createReviewBlock()
    {
        $reviewInformation = $this->getLayout()->createBlock('checkout_onepage_review', 'checkout.review.info');
        
        $block = $this->getLayout()->createBlock('tpl', 'checkout.review')
            ->setTemplate('checkout/onepage/review.phtml')
            ->setChild('reviewInformation', $reviewInformation);
            
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