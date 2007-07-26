<?php
class Mage_Tag_Block_Mytags extends Mage_Core_Block_Template {
	protected $_collection;
    
    public function __construct() {    	
        parent::__construct();
        $this->setTemplate('tag/mytags.phtml');
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        
        $this->_collection = Mage::getModel('tag/tag')->getCollection();
/*
        $this->_collection
            ->addStoreFilter(Mage::getSingleton('core/store')->getId())
            ->addStatusFilter(2)
            ->addEntityFilter('customer', $customerId);           
*/
    }
    
    public function count() {
        return $this->_collection->getSize();
    }
    
    public function toHtml() {
//        $this->_collection->load();        
        
        $this->assign('collection', $this->_collection);
		$this->assign('productId', Mage::registry('controller')->getRequest()->getParam('id', false));
        return parent::toHtml();
    }
}
