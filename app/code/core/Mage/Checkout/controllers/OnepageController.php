<?php

class Mage_Checkout_OnepageController extends Mage_Core_Controller_Front_Action 
{
    protected function _construct()
    {
        parent::_construct();
        $this->setFlag('status', 'no-renderLayout', true);
        $this->setFlag('getAddress', 'no-renderLayout', true);
    }
    
    public function indexAction()
    {
        $statusBlock =  Mage::createBlock('onepage_status', 'checkout.status');
            
        Mage::getBlock('left')->unsetChildren()
            ->insert($statusBlock);
            
        $block = Mage::createBlock('onepage', 'checkout.onepage');
        Mage::getBlock('content')->append($block);
        //$this->_redirect($this->_data['url']['checkout'].'/shipping');
    }
    
    public function statusAction()
    {
        $statusBlock = Mage::createBlock('onepage_status', 'root');
        $this->getResponse()->appendBody($statusBlock->toString());
    }
    
    public function getAddressAction()
    {
        $addressId = $this->getRequest()->getParam('address', false);
        if ($addressId) {
            $address = Mage::getModel('customer', 'address')->getRow($addressId);
            $this->getResponse()->setHeader('Content-type', 'application/x-json');
            $this->getResponse()->setHeader('X-JSON', 'Prototype');
            $this->getResponse()->appendBody($address->__toJson());
        }
    }
    
    public function saveBillingAction()
    {
        
    }
    
    public function savePaymentAction()
    {
        
    }
    
    public function saveShippingAction()
    {
        
    }
    
    public function saveShippingMethodAction()
    {
        
    }
}
