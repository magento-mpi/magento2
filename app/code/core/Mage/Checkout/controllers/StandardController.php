<?php

class Mage_Checkout_StandardController extends Mage_Core_Controller_Front_Action 
{
    function indexAction()
    {
        $this->_redirect(Mage::getbaseUrl('', 'Mage_Checkout').'/standard/shipping');
    }
    
    function shippingAction()
    {
        
    }
    
    function shippingPostAction()
    {
        $this->_redirect(Mage::getbaseUrl('', 'Mage_Checkout').'/standard/payment');
    }
    
    function paymentAction()
    {
        
    }
    
    function paymentPostAction()
    {
        $this->_redirect(Mage::getbaseUrl('', 'Mage_Checkout').'/standard/payment');
    }
    
    function overviewAction()
    {
        
    }
    
    function overviewPostAction()
    {
        $this->_redirect(Mage::getbaseUrl('', 'Mage_Checkout').'/standard/success');
    }
    
    function successAction()
    {
        
    }
}