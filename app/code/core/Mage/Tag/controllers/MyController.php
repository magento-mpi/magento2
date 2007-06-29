<?php
class Mage_Tag_MyController extends Mage_Core_Controller_Front_Action {
	public function indexAction() {        
		$this->loadLayout();
        
        $block = $this->getLayout()->createBlock('tag/mytags')
            ->assign('messages', Mage::getSingleton('customer/session')->getMessages(true));
            
        $this->getLayout()->getBlock('content')->append($block);
        
        $this->renderLayout();
    }
}
?>