<?php

class Mage_Checkout_OnepageController extends Mage_Core_Controller_Front_Action 
{
    
    protected $_data = array();
    protected $_checkout = null;
    protected $_quote = null;
    
    protected function _construct()
    {
        parent::_construct();
        
        if (!($this->getOnepage()->getQuote()->hasItems() || $this->getRequest()->getActionName()!='success')) {
            $this->setFlag('', 'no-dispatch', true);
            $this->_redirect('checkout/cart');
        }
    }
    
    public function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }
    
    /**
     * Checkout page
     */
    public function indexAction()
    {
    	#Mage::getSingleton('customer/session')->setTest('onepage');
    	
        Mage::getSingleton('customer/session')->setUrlBeforeAuthentication($this->getRequest()->getRequestUri());
        $this->getOnepage()->initCheckout();
        $this->loadLayout(array('default', 'onepage'), 'checkout_onepage');
        $this->renderLayout();
    }

    /**
     * Checkout status block
     */
    public function progressAction()
    {
        $this->loadLayout('onepage_progress');
        $this->renderLayout();
    }

    public function shippingMethodAction()
    {
        $this->loadLayout('onepage_shipping');
        $this->renderLayout();
    }
    
    public function reviewAction()
    {
        $this->loadLayout('onepage_review');
        $this->renderLayout();
    }
    
    public function successAction()
    {
    	$lastQuoteId = $this->getOnepage()->getCheckout()->getLastQuoteId();
    	$lastOrderId = $this->getOnepage()->getCheckout()->getLastOrderId();
    	if (!$lastQuoteId || !$lastOrderId) {
            $this->_redirect('checkout/cart');
            return;
        }

        $this->loadLayout();
        
        $block = $this->getLayout()->createBlock('core/template', 'checkout.success')
            ->setTemplate('checkout/success.phtml')
            ->assign('quoteId', $lastQuoteId)
            ->assign('orderId', $lastOrderId);
        $this->getLayout()->getBlock('content')->append($block);
        
        Mage::dispatchEvent('order_success_page_view');
        
        $this->renderLayout();
        Mage::getSingleton('checkout/session')->clear();
    }

    /**
     * Address JSON
     */
    public function getAddressAction()
    {
        $addressId = $this->getRequest()->getParam('address', false);
        if ($addressId) {
            $address = $this->getOnepage()->getAddress($addressId);
            $this->getResponse()->setHeader('Content-type', 'application/x-json');
            $this->getResponse()->setBody($address->toJson());
        }
    }
    
    public function saveMethodAction()
    {
        if ($this->getRequest()->isPost()) {
            $method = $this->getRequest()->getPost('method');
            $result = $this->getOnepage()->saveCheckoutMethod($method);
            $this->getResponse()->setBody(Zend_Json::encode($result));
        }
    }

    /**
     * save checkout billing address
     */
    public function saveBillingAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('billing', array());
            $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);
            $result = $this->getOnepage()->saveBilling($data, $customerAddressId);
            $this->getResponse()->setBody(Zend_Json::encode($result));
        }
    }
    
    public function saveShippingAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping', array());
            $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
            $result = $this->getOnepage()->saveShipping($data, $customerAddressId);
            $this->getResponse()->setBody(Zend_Json::encode($result));
        }
    }
    
    public function saveShippingMethodAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping_method', '');
            $result = $this->getOnepage()->saveShippingMethod($data);
            $this->getResponse()->setBody(Zend_Json::encode($result));
        }

    }
    
    public function savePaymentAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('payment', array());
            $result = $this->getOnepage()->savePayment($data);
            $this->getResponse()->setBody(Zend_Json::encode($result));
        }
    }
    
    public function saveOrderAction()
    {
        $result = $this->getOnepage()->saveOrder();
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }
}