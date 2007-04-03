<?php

class Mage_Checkout_OnepageController extends Mage_Core_Controller_Front_Action 
{
    protected function _construct()
    {
        parent::_construct();
        $this->setFlag('status', 'no-preDispatch', true);
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
        Mage::createBlock('onepage_status', 'root');
    }
}
