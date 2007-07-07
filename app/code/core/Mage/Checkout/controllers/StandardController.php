<?php

class Mage_Checkout_StandardController extends Mage_Core_Controller_Front_Action 
{
    protected $_data = array();
    
    protected function _construct()
    {
        parent::_construct();

        $this->_data['params'] = $this->getRequest()->getParams();
    }
    
    function indexAction()
    {
        $this->_redirect('checkout/standard/shipping');
    }
    
    function shippingAction()
    {
        // check customer auth
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            return;
        }
        
        // TODO: change address id
        $addressId = Mage::getSingleton('customer/session')->getCustomer()->getPrimaryAddress('shipping');
        $address = Mage::getModel('customer/address')->getRow($addressId);
        
        $block = $this->getLayout()->createBlock('core/template', 'checkout.shipping')
            ->setTemplate('checkout/shipping.phtml')
            ->assign('data', $this->_data)
            ->assign('address', $address)
            ->assign('action', Mage::getUrl('checkout/standard/shippingPost'));
        $this->getLayout()->getBlock('content')->append($block);
    }
    
    function shippingPostAction()
    {
        $this->_redirect('checkout/standard/payment');
    }
    
    function paymentAction()
    {
        $block = $this->getLayout()->createBlock('core/template', 'checkout.payment')
            ->setTemplate('checkout/payment.phtml')
            ->assign('data', $this->_data);
        $this->getLayout()->getBlock('content')->append($block);
    }
    
    function paymentPostAction()
    {
        $this->_redirect('checkout/standard/overview');
    }
    
    function overviewAction()
    {
        $block = $this->getLayout()->createBlock('core/template', 'checkout.overview')
            ->setTemplate('checkout/overview.phtml')
            ->assign('data', $this->_data);
        $this->getLayout()->getBlock('content')->append($block);
    }
    
    function overviewPostAction()
    {
        $this->_redirect('checkout/standard/checkout');
    }
    
    function successAction()
    {
        $block = $this->getLayout()->createBlock('core/template', 'checkout.success')
            ->setTemplate('checkout/success.phtml')
            ->assign('data', $this->_data);
        $this->getLayout()->getBlock('content')->append($block);
    }
}