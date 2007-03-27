<?php

class Mage_Checkout_StandardController extends Mage_Core_Controller_Front_Action 
{
    function indexAction()
    {
        $this->_redirect(Mage::getbaseUrl('', 'Mage_Checkout').'/standard/shipping');
    }
    
    function shippingAction()
    {
        $data = array();
        $block = Mage::createBlock('tpl', 'checkout.shipping')
            ->setViewName('Mage_Checkout', 'shipping')
            ->assign('shipping', $data);
        Mage::getBlock('content')->append($block);
    }
    
    function shippingPostAction()
    {
        $this->_redirect(Mage::getbaseUrl('', 'Mage_Checkout').'/standard/payment');
    }
    
    function paymentAction()
    {
        $data = array();
        $block = Mage::createBlock('tpl', 'checkout.payment')
            ->setViewName('Mage_Checkout', 'payment')
            ->assign('shipping', $data);
        Mage::getBlock('content')->append($block);
    }
    
    function paymentPostAction()
    {
        $this->_redirect(Mage::getbaseUrl('', 'Mage_Checkout').'/standard/payment');
    }
    
    function overviewAction()
    {
        $data = array();
        $block = Mage::createBlock('tpl', 'checkout.overview')
            ->setViewName('Mage_Checkout', 'overview')
            ->assign('shipping', $data);
        Mage::getBlock('content')->append($block);
    }
    
    function overviewPostAction()
    {
        $this->_redirect(Mage::getbaseUrl('', 'Mage_Checkout').'/standard/success');
    }
    
    function successAction()
    {
        $data = array();
        $block = Mage::createBlock('tpl', 'checkout.success')
            ->setViewName('Mage_Checkout', 'success')
            ->assign('shipping', $data);
        Mage::getBlock('content')->append($block);
    }
}