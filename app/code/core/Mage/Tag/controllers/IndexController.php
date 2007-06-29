<?php
class Mage_Tag_IndexController extends Mage_Core_Controller_Front_Action {
	public function indexAction() {        
		$this->loadLayout();

        $homeBlock = $this->getLayout()->createBlock('core/template', 'homecontent')->setTemplate('catalog/home.phtml');
        $this->getLayout()->getBlock('content')->append($homeBlock);

        $this->renderLayout();
    }
    
    public function addtagAction() {   	
        $tag_name = $this->getRequest()->getParam('tag_name');
        $entity_val_id = intval($this->getRequest()->getParam('entity_val_id'));
        $entity = $this->getRequest()->getParam('entity');
        try {
	        Mage::getSingleton('tag/tag')
	            ->setEntityId($entity)
	            ->setEntityValId($entity_val_id)
	            ->setTagName($tag_name)
	            ->setStatus(1)
	            ->save();
	            
	        Mage::getSingleton('tag/tag')
	            ->setEntityId('customer')
	            ->setEntityValId(Mage::getSingleton('customer/session')->getCustomerId())
	            ->setTagName($tag_name)
	            ->setStatus(1)
	            ->save();
        } catch (Exception $e) {
        	die(json_encode(array('error' => 1, 'error_message' => $e->getMessage())));
        }
        die(json_encode(array('error' => 0)));
    }
    
    public function deleteAction() {
    	Mage::getSingleton('tag/tag')
    		->setId($this->getRequest()->getParam('tag_id'))
    		->setEntityValId(Mage::getSingleton('customer/session')->getCustomerId())
    		->setEntityId('customer')
    		->delete();
    }
    
    public function updateAction() {
    	Mage::getSingleton('tag/tag')
    		->setId($this->getRequest()->getParam('tag_id'))
    		->setStatus($this->getRequest()->getParam('status'))
    		->setTagName($this->getRequest()->getParam('tagname'))
    		->update();
    }
    
    public function searchAction() {
    	$q = $this->getRequest()->getParam('q');
    	
    	$tags = Mage::getSingleton('tag/tag')->getCollection()
    		->addSearch($q)
    		->load();

    	/***********************************/
    	$this->loadLayout();
/*
        $homeBlock = $this->getLayout()->createBlock('core/template', 'homecontent')->setTemplate('catalog/home.phtml');
        $this->getLayout()->getBlock('content')->append($homeBlock);
  */      
        $block = $this->getLayout()->createBlock('tag/search')
            ->assign('messages', Mage::getSingleton('customer/session')->getMessages(true));
		
        $this->getLayout()->getBlock('content')->append($block);
		 
        $this->renderLayout();
    }
}
?>