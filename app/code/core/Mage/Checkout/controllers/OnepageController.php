<?php

class Mage_Checkout_OnepageController extends Mage_Core_Controller_Front_Action 
{
    function indexAction()
    {
        $processBlock =  Mage::createBlock('tpl', 'checkout.process')
            ->setViewName('Mage_Checkout', 'onepage/process.phtml');
            
        Mage::getBlock('left')->unsetChildren()
            ->insert($processBlock);
            
        $block = Mage::createBlock('tpl', 'checkout.onepage')
            ->setViewName('Mage_Checkout', 'onepage.phtml');
        
        Mage::getBlock('content')->append($block);
        //$this->_redirect($this->_data['url']['checkout'].'/shipping');
    }
    
    function shippingAction()
    {
        // check customer auth
        if (!Mage_Customer_Front::authenticate($this)) {
            return;
        }
        
        // TODO: change address id
        $addressId = Mage_Customer_Front::getCustomerInfo('default_address_id');
        $address = Mage::getModel('customer', 'address')->getRow($addressId);
        
        $block = Mage::createBlock('tpl', 'checkout.shipping')
            ->setViewName('Mage_Checkout', 'shipping.phtml')
            ->assign('data', $this->_data)
            ->assign('address', $address)
            ->assign('action', Mage::getBaseUrl('', 'Mage_Checkout').'/standard/shippingPost/');
        Mage::getBlock('content')->append($block);
    }
}
