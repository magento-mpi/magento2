<?php
class Mage_Taggedproducts_Block_Results extends Mage_Core_Block_Template{
	public function __construct() {
        parent::__construct();
    }

    public function getResults(Zend_Controller_Request_Http $request) {
    	$this->setTemplate('taggedproducts/result.phtml');
        $query = $request->getParam('tag', false);
        $queryEscaped = htmlspecialchars($query);
        Mage::registry('action')->getLayout()->getBlock('head.title')->setContents('Search results for: '.$queryEscaped);     
        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection = $collection->addTagFilter($query);
        $this->assign('hits', $collection);
        $this->assign('query', $queryEscaped);
        
        return $collection;
    }
}