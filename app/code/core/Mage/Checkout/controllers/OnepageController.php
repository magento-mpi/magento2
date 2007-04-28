<?php

class Mage_Checkout_OnepageController extends Mage_Core_Controller_Front_Action 
{
    
    protected $_data = array();
    protected $_checkout = null;
    protected $_quote = null;
    
    protected function _construct()
    {
        parent::_construct();

        $this->_checkout = Mage::getSingleton('checkout', 'session');
        $this->_quote = $this->_checkout->getQuote();
        
        if (!$this->_quote->hasItems()) {
            $this->setFlag('', 'no-dispatch', true);
            $this->_redirect(Mage::getUrl('checkout', array('controller'=>'cart')));
        }
    }
    
    /**
     * Checkout page
     */
    public function indexAction()
    {
        $this->loadLayout();
        
        $statusBlock =  Mage::createBlock('checkout_onepage_status', 'checkout.status');
            
        Mage::getBlock('left')->unsetChildren()
            ->insert($statusBlock);
            
        $block = Mage::createBlock('checkout_onepage', 'checkout.onepage');
        Mage::getBlock('content')->append($block);
        
        $this->renderLayout();
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
    
    public function reviewAction()
    {
        $block = Mage::createBlock('checkout_onepage_review', 'root');
        
        $this->getResponse()->appendBody($block->toString());
    }
    
    public function successAction()
    {
        $this->loadLayout();
        $block = Mage::createBlock('tpl', 'checkout.success')
            ->setTemplate('checkout/success.phtml')
            ->assign('orderId', $this->_quote->getQuoteId());
        Mage::getBlock('content')->append($block);
        
        // TODO: clear quote and checkout 
        //$this->_checkout->clear();
        
        $this->renderLayout();
    }

    /**
     * Address JSON
     */
    public function getAddressAction()
    {
        $addressId = $this->getRequest()->getParam('address', false);
        if ($addressId) {
            $address = Mage::getModel('customer', 'address')->load((int)$addressId);
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
            $address = Mage::getModel('sales', 'quote_entity_address')->addData($data);
            $address->implodeStreetAddress();
            $this->_quote->setBillingAddress($address);
            $this->_quote->save();

            $this->_checkout->setAllowBilling(true);
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
            $payment = Mage::getModel('sales', 'quote_entity_payment')->addData($data);
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
            $address = Mage::getModel('sales', 'quote_entity_address')->addData($data);
            $address->implodeStreetAddress();
            $this->_quote->setShippingAddress($address);
            $this->_quote->save();

            $this->_checkout->setShippingMethods(null);
            $this->_checkout->setCompletedShipping(true);
            $this->_checkout->setAllowShippingMethod(true);
        }
    }
    
    public function saveShippingMethodAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping_method', '');
            if (empty($data)) {
                return;
            }
            $this->_quote->setShippingMethod($data);
            $this->_quote->save();
            
            $this->_checkout->setCompletedShippingMethod(true);
            $this->_checkout->setAllowReview(true);
        }

    }
    
    public function saveOrderAction()
    {
        $res = array('error'=>1);
        if ($this->getRequest()->isPost()) {
            try {
                $this->_quote->createOrders();
                $res['success'] = true;
                $res['error']   = false;
            }
            catch (Exception $e){
                // TODO: create responce for open checkout card with error
                
            }
        }
        
        $this->getResponse()->setHeader('Content-type', 'application/x-json');
        $this->getResponse()->appendBody(Zend_Json::encode($res));
    }
}