<?php

class Mage_Checkout_StandardController extends Mage_Core_Controller_Front_Action 
{
    protected $_data = array();
    
    protected function _construct()
    {
        parent::_construct();
        
        $this->_data['url']['base'] = Mage::getBaseUrl();
        $this->_data['url']['checkout'] = Mage::getBaseUrl('', 'Mage_Checkout').'/standard';
        
        $this->_data['params'] = $this->getRequest()->getParams();
    }
    
    function indexAction()
    {
        $this->_redirect($this->_data['url']['checkout'].'/shipping');
    }
    
    function shippingAction()
    {
        $block = Mage::createBlock('tpl', 'checkout.shipping')
            ->setViewName('Mage_Checkout', 'shipping')
            ->assign('data', $this->_data);
        Mage::getBlock('content')->append($block);
    }
    
    function shippingPostAction()
    {
        $this->_redirect($this->_data['url']['checkout'].'/payment');
    }
    
    function paymentAction()
    {
        $block = Mage::createBlock('tpl', 'checkout.payment')
            ->setViewName('Mage_Checkout', 'payment')
            ->assign('data', $this->_data);
        Mage::getBlock('content')->append($block);
    }
    
    function paymentPostAction()
    {
        $this->_redirect($this->_data['url']['checkout'].'/overview');
    }
    
    function overviewAction()
    {
        $block = Mage::createBlock('tpl', 'checkout.overview')
            ->setViewName('Mage_Checkout', 'overview')
            ->assign('data', $this->_data);
        Mage::getBlock('content')->append($block);
    }
    
    function overviewPostAction()
    {
        $this->_redirect($this->_data['url']['checkout'].'/success');
    }
    
    function successAction()
    {
        $block = Mage::createBlock('tpl', 'checkout.success')
            ->setViewName('Mage_Checkout', 'success')
            ->assign('data', $this->_data);
        Mage::getBlock('content')->append($block);
    }
}