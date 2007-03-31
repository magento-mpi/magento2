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
    
    public function processStatusAction()
    {
        
    }
}
