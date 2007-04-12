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
        $block = Mage::createBlock('checkout_shipping_method', 'root');
        
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
            if (empty($data)) {
                return;
            }
            $address = Mage::getModel('customer', 'address')->setData($data);
            $address->implodeStreetAddress();
            $this->_quote->setAddress('billing', $address);
            $this->_quote->save();

            $this->_checkout->setCompletedBilling(true);
            $this->_checkout->setAllowPayment(true);
        }
    }
    
    public function savePaymentAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('payment', array());
            if (empty($data)) {
                return;
            }
            $payment = Mage::getModel('customer', 'payment')->setData($data);
            $payment->setCcNumber($payment->encrypt($payment->getCcNumber()));
            $this->_quote->setPayment($payment);
            $this->_quote->save();
            
            $this->_checkout->setCompletedPayment(true);
            $this->_checkout->setAllowShipping(true);
        }
    }
    
    public function saveShippingAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping', array());
            if (empty($data)) {
                return;
            }
            $address = Mage::getModel('customer', 'address')->setData($data);
            $address->implodeStreetAddress();
            $this->_quote->setAddress('shipping', $address);
            $this->_quote->save();

            $this->_checkout->setShippingMethods(null);
            $this->_checkout->setCompletedShipping(true);
            $this->_checkout->setAllowShippingMethod(true);
        }
    }
    
    public function saveShippingMethodAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping_method', array());
            if (empty($data)) {
                return;
            }
            $this->_quote->getAddressByType('shipping')->setAttribute('shipping_method', $data['method']);
            $this->_quote->save();
            
            $this->_checkout->setCompletedShippingMethod(true);
            $this->_checkout->setAllowOrderReview(true);
        }

    }
}