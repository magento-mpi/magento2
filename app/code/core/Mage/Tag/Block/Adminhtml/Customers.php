<?php
class Mage_Tag_Block_Adminhtml_Customers extends Mage_Adminhtml_Block_Widget_Tabs {
	protected $_collection;
    
    public function __construct() {    	
        parent::__construct();
        $this->setTemplate('tag/adminhtml/customers.phtml');
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        $query = Mage::registry('controller')->getRequest()->getParam('tagbyuser', false);
        if (!empty($query)) {
        	/*
	        $this->_collection = Mage::getResourceModel('customer/customer_collection')
	            ->setNameFilter($query);
        	*/
        	$entity = Mage::getModel('eav/entity')->setType('customer')->loadAllAttributes();
        	$customer = Mage::getModel('customer/customer');
        	
        	$entity->load($customer, $customerId);        	
        	
        	$collection = Mage::getModel('customer/entity_customer_collection')
	            ->setEntity($entity)->setObject($customer)
	            ->addAttributeToSelect('*')
	            ->addAttributeToFilter('firstname', array('like'=>'%'))	            
	            ->setPage(1,10);
	            
	        $this->_collection = $collection;	         
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
		            ->addEntityFilter('customer', $dt['customer_id'])
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
		$this->assign('query', Mage::registry('controller')->getRequest()->getParam('tagbyuser', false));
        return parent::toHtml();
    }
}