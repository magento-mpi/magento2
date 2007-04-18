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
        $this->_redirect(Mage::getUrl('checkout', array('controller'=>'standard', 'action'=>'shipping'));
    }
    
    function shippingAction()
    {
        // check customer auth
        if (!Mage::getSingleton('customer_model', 'session')->authenticate($this)) {
            return;
        }
        
        // TODO: change address id
        $addressId = Mage::getSingleton('customer_model', 'session')->getCustomer()->getPrimaryAddress('shipping');
        $address = Mage::getModel('customer', 'address')->getRow($addressId);
        
        $block = Mage::createBlock('tpl', 'checkout.shipping')
            ->setViewName('Mage_Checkout', 'shipping.phtml')
            ->assign('data', $this->_data)
            ->assign('address', $address)
            ->assign('action', Mage::getUrl('checkout', array('controller'=>'standard', 'action'=>'shippingPost')));
        Mage::getBlock('content')->append($block);
    }
    
    function shippingPostAction()
    {
        $this->_redirect(Mage::getUrl('checkout', array('controller'=>'standard', 'action'=>'payment'));
    }
    
    function paymentAction()
    {
        $block = Mage::createBlock('tpl', 'checkout.payment')
            ->setViewName('Mage_Checkout', 'payment.phtml')
            ->assign('data', $this->_data);
        Mage::getBlock('content')->append($block);
    }
    
    function paymentPostAction()
    {
        $this->_redirect(Mage::getUrl('checkout', array('controller'=>'standard', 'action'=>'overview'));
    }
    
    function overviewAction()
    {
        $block = Mage::createBlock('tpl', 'checkout.overview')
            ->setViewName('Mage_Checkout', 'overview.phtml')
            ->assign('data', $this->_data);
        Mage::getBlock('content')->append($block);
    }
    
    function overviewPostAction()
    {
        $this->_redirect(Mage::getUrl('checkout', array('controller'=>'standard', 'action'=>'checkout'));
    }
    
    function successAction()
    {
        $block = Mage::createBlock('tpl', 'checkout.success')
            ->setViewName('Mage_Checkout', 'success.phtml')
            ->assign('data', $this->_data);
        Mage::getBlock('content')->append($block);
    }
}