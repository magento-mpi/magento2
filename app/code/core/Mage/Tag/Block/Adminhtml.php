<?php
/*
class Mage_Tag_Block_Adminhtml extends Mage_Core_Block_Template {
	protected $_collection;
    
    public function __construct() {    	
        parent::__construct();
        $this->setTemplate('tag/adminhtml/index.phtml');
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        
        $this->_collection = Mage::getModel('tag/tag')->getCollection();
        $this->_collection
            ->addStoreFilter(Mage::getSingleton('core/store')->getId())
            ->addStatusFilter(1)
            ->addEntityFilter('customer', $customerId);
    }
    
    public function count() {
        return $this->_collection->getSize();
    }
    
    public function toHtml() {
        $this->_collection->load();        
        $this->assign('collection', $this->_collection);
		$this->assign('productId', Mage::registry('controller')->getRequest()->getParam('id', false));
        return parent::toHtml();
    }
}
*/
class Mage_Tag_Block_Adminhtml extends Mage_Adminhtml_Block_Widget_Tabs {   
    public function __construct()
    {
        parent::__construct();
        $this->setId('role_info_tabs');
        $this->setDestElementId('tag_form');
    }
    
    protected function _beforeToHtml()
    {
    	$this->addTab('pending', array(
            'label'     => __('Pending Tags'),
            'title'     => __('Pending Tags'),
            'content'   => $this->getLayout()->createBlock('tag/adminhtml_pending')->toHtml(),
            'active' 	=> true
        ));
        
        $this->addTab('tagslist', array(
            'label'     => __('Tags List'),
            'title'     => __('Tags List'),
            'content'   => $this->getLayout()->createBlock('tag/adminhtml_tagslist')->toHtml(),
        ));
        
        $this->addTab('products', array(
            'label'     => __('Tagged Products'),
            'title'     => __('Tagged Products'),
            'content'   => $this->getLayout()->createBlock('tag/adminhtml_products')->toHtml(),
        ));
        
        $this->addTab('customers', array(
            'label'     => __('Tags by Customers'),
            'title'     => __('Tags by Customers'),
            'content'   => $this->getLayout()->createBlock('tag/adminhtml_customers')->toHtml(),
        ));
  
        return parent::_beforeToHtml();
    }
}