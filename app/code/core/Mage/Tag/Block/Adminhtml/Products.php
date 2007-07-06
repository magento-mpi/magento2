<?php
class Mage_Tag_Block_Adminhtml_Products extends Mage_Core_Block_Template {
	protected $_collection;
    
    public function __construct() {    	
        parent::__construct();
        $this->setTemplate('tag/adminhtml/products.phtml');
        $query = Mage::registry('controller')->getRequest()->getParam('q', false);
        
        $tag = Mage::registry('controller')->getRequest()->getParam('t', false);       
        
        if (!empty($tag)) {
        	$v = Mage::getResourceModel('catalog/product_collection');
	        $items = $v	        	
	            ->addTagFilter($tag)
	            ->load();
	        
	        $ids = array();
	        foreach ($items->getItems() as $items) {
	        	$ids[] = $items->getData('product_id');
	        }
	        
	        $v = Mage::getResourceModel('catalog/product_collection');
	        $this->_collection = $v	        	
	            ->addIdFilter($ids);
        }
        
        if (!empty($query)) {
	        $v = Mage::getResourceModel('catalog/product_collection');
	        $this->_collection = $v	        	
	            ->addSearchFilter($query);
        }
    }
    
    public function count() {
        return $this->_collection->getSize();
    }
    
    public function toHtml() {
    	if (!empty($this->_collection)) {
        	$this->_collection
        		->load();
        		
        	$coll = array();
        	foreach ($this->_collection->getItems() as $item) {
        		$dt = $item->getData();
        		
        		$var = Mage::getModel('tag/tag')->getCollection()
		            ->addStoreFilter(Mage::getSingleton('core/store')->getId())
		            ->addStatusFilter(2)
		            ->addEntityFilter('product', $dt['product_id'])
		            ->load();
		            
		            $tags = array();
		            foreach ($var->getItems() as $tag) {
		            	$tag = $tag->getData();
		            	$tags[] = $tag;
		            }
		            
        		$coll[] = array_merge($dt, array('tags' => $tags));
        	}
        	$this->assign('collection', $coll);
    	}
		$this->assign('query', Mage::registry('controller')->getRequest()->getParam('q', false));
        return parent::toHtml();
    }
}