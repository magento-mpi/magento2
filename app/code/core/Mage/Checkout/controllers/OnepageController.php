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
    
    public function indexAction()
    {
        $statusBlock =  Mage::createBlock('onepage_status', 'checkout.status');
            
        Mage::getBlock('left')->unsetChildren()
            ->insert($statusBlock);
            
        $block = Mage::createBlock('onepage', 'checkout.onepage');
        Mage::getBlock('content')->append($block);
    }
    
    public function statusAction()
    {
        $statusBlock = Mage::createBlock('onepage_status', 'root');
        $this->getResponse()->appendBody($statusBlock->toString());
    }
    
    public function shippingMethodAction()
    {
        $data = Mage::registry('Mage_Checkout')->getStateData('shipping_method');

        $block = Mage::createBlock('tpl', 'checkout.shipping_method')
            ->setViewName('Mage_Checkout', 'onepage/shipping_method.phtml')
            ->assign('data', $data);
        
        $this->getResponse()->appendBody($block->toString());
    }
    
    public function getAddressAction()
    {
        $addressId = $this->getRequest()->getParam('address', false);
        if ($addressId) {
            $address = new Mage_Customer_Address((int) $addressId);
            $this->getResponse()->setHeader('Content-type', 'application/x-json');
            $this->getResponse()->appendBody($address->__toJson());
        }
    }
    
    public function saveBillingAction()
    {
        $checkout = Mage::registry('Mage_Checkout');
        if ($this->getRequest()->isPost()) {
            $data = isset($_POST['billing']) ? $_POST['billing'] : array();
            if (!empty($data)) {
                $checkout->setStateData('billing', 'allow', true);
            }
            $checkout->setStateData('billing', 'data', $data);
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
            $checkout->setStateData('shipping', 'data', $data);
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
