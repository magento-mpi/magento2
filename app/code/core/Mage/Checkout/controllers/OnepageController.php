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
        $this->_checkout->setCompletedBilling(false);
        $this->_checkout->setCompletedPayment(false);
        $this->_checkout->setCompletedShipping(false);
        $this->_checkout->setCompletedShippingMethod(false);
        $this->_checkout->setAllowPayment(false);
        $this->_checkout->setAllowShipping(false);
        $this->_checkout->setAllowShippingMethod(false);
        $this->_checkout->setAllowReview(false);
        
        $this->loadLayout('front', array('default', 'checkout'), 'checkout');
        
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
        
        /*
        $customerSession = Mage::getSingleton('customer', 'session');
        if (!$customerSession->isLoggedIn()) {
            $this->_redirect(Mage::getUrl('checkout', array('controller'=>'cart')));
            return;
        }
        $collection = Mage::getModel('sales_resource', 'order_collection')
            ->addAttributeSelect('self/real_order_id')
            ->addAttributeFilter('self/customer_id', $customerSession->getCustomerId())
            ->setOrder('self/created_at', 'DESC')
            ->setPageSize(1)
            ->loadData();
        foreach ($collection as $order) {
            $orderId = $order->getRealOrderId();
        }
        */
        $order = Mage::getModel('sales', 'order');
        $order->load($this->_checkout->getLastOrderId());
        if (!$order->getRealOrderId()) {
            $this->_redirect(Mage::getUrl('checkout', array('controller'=>'cart')));
            return;
        }
        $orderId = $order->getRealOrderId();
        
        $block = $this->getLayout()->createBlock('tpl', 'checkout.success')
            ->setTemplate('checkout/success.phtml')
            ->assign('orderId', $orderId);
        $this->getLayout()->getBlock('content')->append($block);
        
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
            if ($address->getRegionId()) {
                $address->setRegion($address->getRegionId());
            }
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
                $orderId = $this->_quote->getCreatedOrderId();
                $this->_checkout->clear();
                $this->_checkout->setLastOrderId($orderId);
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