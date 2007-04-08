<?php

class Mage_Checkout_OnepageController extends Mage_Core_Controller_Front_Action 
{
    protected $_checkout = null;
    
    protected function _construct()
    {
        parent::_construct();
        
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
        $checkout = Mage::registry('Mage_Checkout');

        #$checkout->fetchShippingMethods();

        $quotes = $checkout->getStateData('shipping_method', 'quotes');
        $data = $checkout->getStateData('shipping_method', 'data');

        $block = Mage::createBlock('tpl', 'root')
	        ->setViewName('Mage_Checkout', 'onepage/shipping_method/box.phtml')
	        ->assign('quotes', $quotes)
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
            $address = new Mage_Customer_Address((int) $addressId);
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
        $checkout = Mage::registry('Mage_Checkout');
        if ($this->getRequest()->isPost()) {
            $data = isset($_POST['billing']) ? $_POST['billing'] : array();
            if (!empty($data)) {
                $checkout->setStateData('billing', 'allow', true);
            }
            $address = new Mage_Customer_Address($data);
            $checkout->setStateData('billing', 'address', $address);
        }
    }
    
    public function savePaymentAction()
    {
        $checkout = Mage::registry('Mage_Checkout');
        if ($this->getRequest()->isPost()) {
            $data = isset($_POST['payment']) ? $_POST['payment'] : array();
            if (!empty($data)) {
                $checkout->setStateData('payment', 'allow', true);
            }
            $checkout->setStateData('payment', 'data', $data);
        }
    }
    
    public function saveShippingAction()
    {
        $checkout = Mage::registry('Mage_Checkout');
        if ($this->getRequest()->isPost()) {
            $data = isset($_POST['shipping']) ? $_POST['shipping'] : array();
            if (!empty($data)) {
                $checkout->setStateData('shipping', 'allow', true);
            }
            $address = new Mage_Customer_Address($data);
            $checkout->setStateData('shipping', 'address', $address);
        }

        $checkout->fetchShippingMethods();
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