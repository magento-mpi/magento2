<?php

/**
 * Product search result block
 *
 * @package    Mage
 * @module     Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_CatalogSearch_Block_By_Query extends Mage_Core_Block_Template
{
	protected $_productCollection;
	
    public function __construct()
    {
        parent::__construct();
		$this->setTemplate('catalog/search/result.phtml');
    }

    protected function _initChildren()
    {
        $query = $this->getQuery();
        $queryEscaped = htmlspecialchars($query);

        $pager = $this->getLayout()->createBlock('page/html_pager', 'pager')
            ->setCollection($this->_getProductCollection())
            ->setUrlPrefix('catalog')
            ->setViewBy('mode', array('grid', 'list'))
            ->setViewBy('limit')
            ->setViewBy('order', array('name', 'price'));
        $this->setChild('pager', $pager);

        // add Home breadcrumb
    	$this->getLayout()->getBlock('breadcrumbs')
            ->addCrumb('home',
                array('label'=>__('Home'),
                    'title'=>__('Go to Home Page'),
                    'link'=>Mage::getBaseUrl())
                );
        
        $this->getLayout()->getBlock('head')->setTitle('Search results for: '.$queryEscaped);
        $this->getLayout()->getBlock('root')->setHeaderTitle('Search results for: '.$queryEscaped);            
        
    }
    
    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getProductCollection()
    {
    	if (empty($this->_productCollection)) {
    		$request = $this->getRequest();

    		$query = $this->getQuery();	
	
	        $page = $request->getParam('p',1);
	        $this->_productCollection = Mage::getResourceModel('catalog/product_collection')
	            ->addAttributeToSelect('name')
	            ->addAttributeToSelect('price')
	            ->addAttributeToSelect('description')
	            ->addAttributeToSelect('image')
	            ->addAttributeToSelect('small_image')
	            ->addSearchFilter($query)
	            ->setOrder($request->getParam('order','name'), $request->getParam('dir','asc'))
	            ->setCurPage($request->getParam('p',1))
	            ->setPageSize(9)
	            ->load();
	            
	        $this->setNumResults($this->_productCollection->getSize());
    	}
        
        return $this->_productCollection;
    }
    
    public function hasResults()
    {
    	$this->getLoadedProductCollection();
    	return $this->getNumResults()>0;
    }
    
    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getLoadedProductCollection()
    {
        $collection = $this->_getProductCollection();
        /**
         * @todo isLoaded for collection
         */
        if (!$collection->getSize()) {
            $collection->load();
        }
        return $collection;
    }
    
    /**
     * Retrieve collection pager HTML
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    
    /**
     * Retrieve current view mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->getRequest()->getParam('mode');
    }
    
    /**
     * Retrieve 
     *
     * @return unknown
     */
    public function getCompareJsObjectName()
    {
    	if($this->getLayout()->getBlock('catalog.compare.sidebar')) {
    		return $this->getLayout()->getBlock('catalog.compare.sidebar')->getJsObjectName();
    	} 
    	
    	return false;
    }
    
}