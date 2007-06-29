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
    	$this->loadLayout();
      
        $block = $this->getLayout()->createBlock('tag/search');		
        $this->getLayout()->getBlock('content')->append($block);
		 
        $this->renderLayout();
    }
    
    public function xmlAction() {
    	$r_tag = $this->getRequest()->getParam('tag');
    	$collection = Mage::getResourceModel('catalog/product_collection')
            ->addTagFilter($r_tag)
            ->load();
            
		foreach ($collection->getItems() as $item) {
        	$item = $item->getData();
        	$dt = Mage::getModel('catalog/product')        				
		        		->load($item['product_id'])->getData();
		        		
        	$var = Mage::getModel('tag/tag')->getCollection()
			        	->addStoreFilter(Mage::getSingleton('core/store')->getId())
			        	->addStatusFilter(2)
			        	->addEntityFilter('product', $item['product_id'])
			        	->load();

        	$tags = array();
        	foreach ($var->getItems() as $tag) {
        		$tags[] = $tag->getData();
        	}
		            
        	$coll[] = array_merge($dt, array('tags' => $tags));
        }
        
        
        $doc = new DOMDocument("1.0", "utf-8");
        $root = $doc->createElement("rss");
        
        $root->setAttribute("version", "2.0");
        $doc->appendChild($root);
        
        $channel = $doc->createElement("channel");
        $root->appendChild($channel);
        
        $channel->appendChild($doc->createElement("title", "Products tagged with TAG \"{$r_tag}\""));
        $channel->appendChild($doc->createElement("generator", "Generic generator by Varien"));
        
        foreach ($coll as $product) {
        	$item = $doc->createElement("item");
        	$channel->appendChild($item);
        	
        	$item->appendChild($doc->createElement("title", htmlspecialchars($product['name'])));
        	$item->appendChild($doc->createElement("description", htmlspecialchars($product['description'])));
        	$item->appendChild($doc->createElement("link", "http://magento-alexey.kiev-dev/catalog/product/view/id/{$product['product_id']}/"));
        }
        header("Content-type: application/xhtml+xml");
        echo $doc->saveXML();
    }
}
?>