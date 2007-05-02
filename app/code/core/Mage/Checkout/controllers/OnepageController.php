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
        
        if (!$this->_quote->hasItems() && $this->getRequest()->getParam('action')!='success') {
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
        
        $this->_checkout->setCompletedBilling(false);
        $this->_checkout->setCompletedPayment(false);
        $this->_checkout->setCompletedShipping(false);
        $this->_checkout->setCompletedShippingMethod(false);
        $this->_checkout->setAllowPayment(false);
        $this->_checkout->setAllowShipping(false);
        $this->_checkout->setAllowShippingMethod(false);
        $this->_checkout->setAllowReview(false);
        
        $statusBlock =  $this->getLayout()->createBlock('checkout_onepage_status', 'checkout.status');
            
        $this->getLayout()->getBlock('left')->unsetChildren()
            ->insert($statusBlock);
            
        $block = $this->getLayout()
            ->createBlock('checkout_onepage', 'checkout.onepage')
            ->init();
        
        $this->getLayout()->getBlock('content')->append($block);
        
        $this->renderLayout();
    }

    /**
     * Checkout status block
     */
    public function statusAction()
    {
        $statusBlock = $this->getLayout()->createBlock('checkout_onepage_status', 'root');
        $this->getResponse()->appendBody($statusBlock->toHtml());
    }

    /**
     * Shipping methos tab
     */
    public function shippingMethodAction()
    {
        $block = $this->getLayout()->createBlock('checkout_shipping_method', 'root');
        
        $this->getResponse()->appendBody($block->toHtml());
    }
    
    public function reviewAction()
    {
        $block = $this->getLayout()->createBlock('checkout_onepage_review', 'root');
        
        $this->getResponse()->appendBody($block->toHtml());
    }
    
    public function successAction()
    {
        $this->loadLayout();
        $block = $this->getLayout()->createBlock('tpl', 'checkout.success')
            ->setTemplate('checkout/success.phtml')
            ->assign('orderId', $this->_quote->getQuoteId());
        $this->getLayout()->getBlock('content')->append($block);
        
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
            $this->_quote->collectAllShippingMethods();
            $this->_quote->save();

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
                $this->_checkout->setQuoteId(null);
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