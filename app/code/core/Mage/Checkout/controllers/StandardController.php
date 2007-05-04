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
        $this->getResponse()->setRedirect(Mage::getUrl('checkout', array('controller'=>'standard', 'action'=>'shipping')));
    }
    
    function shippingAction()
    {
        // check customer auth
        if (!Mage::getSingleton('customer', 'session')->authenticate($this)) {
            return;
        }
        
        // TODO: change address id
        $addressId = Mage::getSingleton('customer', 'session')->getCustomer()->getPrimaryAddress('shipping');
        $address = Mage::getModel('customer', 'address')->getRow($addressId);
        
        $block = $this->getLayout()->createBlock('tpl', 'checkout.shipping')
            ->setTemplate('checkout/shipping.phtml')
            ->assign('data', $this->_data)
            ->assign('address', $address)
            ->assign('action', Mage::getUrl('checkout', array('controller'=>'standard', 'action'=>'shippingPost')));
        $this->getLayout()->getBlock('content')->append($block);
    }
    
    function shippingPostAction()
    {
        $this->getResponse()->setRedirect(Mage::getUrl('checkout', array('controller'=>'standard', 'action'=>'payment')));
    }
    
    function paymentAction()
    {
        $block = $this->getLayout()->createBlock('tpl', 'checkout.payment')
            ->setTemplate('checkout/payment.phtml')
            ->assign('data', $this->_data);
        $this->getLayout()->getBlock('content')->append($block);
    }
    
    function paymentPostAction()
    {
        $this->getResponse()->setRedirect(Mage::getUrl('checkout', array('controller'=>'standard', 'action'=>'overview')));
    }
    
    function overviewAction()
    {
        $block = $this->getLayout()->createBlock('tpl', 'checkout.overview')
            ->setTemplate('checkout/overview.phtml')
            ->assign('data', $this->_data);
        $this->getLayout()->getBlock('content')->append($block);
    }
    
    function overviewPostAction()
    {
        $this->getResponse()->setRedirect(Mage::getUrl('checkout', array('controller'=>'standard', 'action'=>'checkout')));
    }
    
    function successAction()
    {
        $block = $this->getLayout()->createBlock('tpl', 'checkout.success')
            ->setTemplate('checkout/success.phtml')
            ->assign('data', $this->_data);
        $this->getLayout()->getBlock('content')->append($block);
    }
}