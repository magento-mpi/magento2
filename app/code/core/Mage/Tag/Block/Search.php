<?php
class Mage_Tag_Block_Search extends Mage_Core_Block_Template {
	protected $_collection;
    
    public function __construct() {    	
        parent::__construct();
        
        $q = Mage::registry('controller')->getRequest()->getParam('q', false);
    	
        $this->setTemplate('tag/search.phtml');
        
        $this->_collection = Mage::getResourceModel('catalog/product_collection')
            ->addTagFilter($q);
    }
    
    public function count() {
        return $this->_collection->getSize();
    }
    
    public function toHtml() {
        $this->_collection->load();
        $coll = array();
        foreach ($this->_collection->getItems() as $item) {
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
        $this->assign('collection', $coll);
        $this->assign('query', Mage::registry('controller')->getRequest()->getParam('q', false));
        return parent::toHtml();
    }
}
?>