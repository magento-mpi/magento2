<?php
class Mage_Tag_Block_Adminhtml_Products extends Mage_Core_Block_Template {
	protected $_collection;
    
    public function __construct() {    	
        parent::__construct();
        $this->setTemplate('tag/adminhtml/products.phtml');
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        $query = Mage::registry('controller')->getRequest()->getParam('q', false);
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