<?php
class Mage_Tag_Block_Adminhtml_Tagslist extends Mage_Core_Block_Template {
	protected $_collection;
    
    public function __construct() {    	
        parent::__construct();
        $this->setTemplate('tag/adminhtml/tagslist.phtml');

        $query = Mage::registry('controller')->getRequest()->getParam('q', false);
        
        
	    $v = Mage::getResourceModel('tag/tag_collection');
	    $this->_collection = $v	        	
	        ->addSearch($query);
    }    
    
    public function toHtml() {
    	$var = $this->_collection
	    	->addStatusFilter(2)
	    	->load();
		            
		$this->assign('tags', $var);
        return parent::toHtml();
    }
}