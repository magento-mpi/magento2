<?php

class Mage_Checkout_OnepageController extends Mage_Core_Controller_Front_Action 
{
    
    protected $_data = array();
    protected $_checkout = null;
    protected $_quote = null;
    
    protected function _construct()
    {
        parent::_construct();
        
        $this->_data['url']['base'] = Mage::getBaseUrl();
        $this->_data['url']['cart'] = Mage::getBaseUrl('', 'Mage_Checkout').'/cart/';
        $this->_data['url']['checkout'] = Mage::getBaseUrl('', 'Mage_Checkout').'/';

        $this->_checkout = Mage::getSingleton('checkout_model', 'session');
        $this->_quote = $this->_checkout->getQuote();
        
        if (!$this->_quote->hasItems()) {
            $this->setFlag('', 'no-renderLayout', true);
            $this->setFlag('', 'no-dispatch', true);
            $this->_redirect($this->_data['url']['cart']);
        }
        
        foreach (array('status','shippingMethod','getAddress','saveBilling','savePayment','saveShipping','saveShippingMethod') as $action) {
            $this->setFlag($action, 'no-renderLayout', true);
        }
    }
    
    /**
     * Checkout page
     */
    public function indexAction()
    {
        $statusBlock =  Mage::createBlock('checkout_onepage_status', 'checkout.status');
            
        Mage::getBlock('left')->unsetChildren()
            ->insert($statusBlock);
            
        $block = Mage::createBlock('checkout_onepage', 'checkout.onepage');
        Mage::getBlock('content')->append($block);
    }

    /**
     * Checkout status block
     */
    public function statusAction()
    {
        $statusBlock = Mage::createBlock('checkout_onepage_status', 'root');
        $this->getResponse()->appendBody($statusBlock->toString());
    }

    /**
     * Shipping methos tab
     */
    public function shippingMethodAction()
    {
        $methods = $this->_checkout->getShippingMethods();
        $data = $this->_quote->getEntityByType('address');

        $block = Mage::createBlock('tpl', 'root')
	        ->setViewName('Mage_Checkout', 'onepage/shipping_method/box.phtml')
	        ->assign('methods', $methods)
	        ->assign('data', $data);
        
        $this->getResponse()->appendBody($block->toString());
    }

    /**
     * Address JSON
     */
    public function getAddressAction()
    {
        $addressId = $this->getRequest()->getParam('address', false);
        if ($addressId) {
            $address = Mage::getModel('customer', 'address');
            $address->load((int) $addressId);
            $address->explodeStreetAddress();
            $this->getResponse()->setHeader('Content-type', 'application/x-json');
            $this->getResponse()->appendBody($address->__toJson());
        }
    }

    /**
     * save checkout billing address
     */
    public function saveBillingAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('billing', array());
            if (!empty($data)) {
                $this->_checkout->setAllowBilling(true);
            }
            $address = Mage::getModel('customer', 'address');
            $address->setData($data);
            $address->implodeStreetAddress();
            $this->_quote->setAddress('billing', $address);
        }
    }
    
    public function savePaymentAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('payment', array());
            if (!empty($data)) {
                $this->_checkout->setAllowPayment('payment', true);
            }
            $payment = Mage::getModel('customer', 'payment')->setData($data);
            $this->_quote->setPayment($payment);
        }
    }
    
    public function saveShippingAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping', array());
            if (!empty($data)) {
                $this->_checkout->setAllowShipping(true);
            }
            $address = Mage::getModel('customer', 'address');
            $address->setData($data);
            $address->implodeStreetAddress();
            $this->_quote->setAddress('shipping', $address);
            $methods = $this->_checkout->collectShippingMethods();
            
        }
    }
    
    public function saveShippingMethodAction()
    {
        $checkout = Mage::registry('Mage_Checkout');
        if ($this->getRequest()->isPost()) {
            $data = isset($_POST['shipping_method']) ? $_POST['shipping_method'] : array();
            if (!empty($data)) {
                $checkout->setStateData('shipping_method', 'allow', true);
            }
            $checkout->setStateData('shipping_method', 'data', $data);
        }

    }
}