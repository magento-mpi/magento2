<?php

/**
 * Product search result block
 *
 * @package    Mage
 * @module     Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_CatalogSearch_Block_Search extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
    }

    public function loadByQuery(Zend_Controller_Request_Http $request)
    {
        $this->setTemplate('catalog/search/result.phtml');
        $query = $this->getQuery();
        
        #$model = Mage::getModel('catalogsearch/model')->
        $queryEscaped = htmlspecialchars($query);

        $this->getLayout()->getBlock('head')->setTitle('Search results for: '.$queryEscaped);
        $this->getLayout()->getBlock('root')->setHeaderTitle('Search results for: '.$queryEscaped);            

        $page = $request->getParam('p',1);
        $prodCollection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('description')
            ->addAttributeToSelect('image')
            ->addAttributeToSelect('small_image')
            ->addSearchFilter($query)
            ->setOrder($request->getParam('order','name'), $request->getParam('dir','asc'))
            ->setCurPage($page)
            ->setPageSize(9)
            ->load();
            
        $numResults = $prodCollection->getSize();
        if ($numResults>0) {
//            Mage::getModel('catalog/search')->updateSearch($query, $numResults);
        }

        $this->assign('query', $queryEscaped);
        $this->assign('productCollection', $prodCollection);

        $pageUrl = clone $request;
        $this->assign('pageUrl', $pageUrl);
        
        $sortUrl = clone $request;
        $sortUrl->setParam('p', 1)->setParam('dir', 'asc');
        $this->assign('sortUrl', $sortUrl);
        
        $this->assign('sortValue', $request->getParam('order','name').'_'.$request->getParam('dir','asc'));
    }
    
    protected $_productCollection;
    
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/category/view.phtml');
    }
    
    protected function _initChildren()
    {
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
        
        $path = $this->getCurrentCategory()->getPathInStore();
        $pathIds = array_reverse(explode(',', $path));
        
        $categories = Mage::getResourceModel('catalog/category_collection')
            ->addAttributeToSelect('name')
            ->addFieldToFilter('entity_id', array('in'=>$pathIds))
            ->load()
            ->getItems();
            
        // add category path breadcrumb
        foreach ($pathIds as $categoryId) {
            if (isset($categories[$categoryId]) && $categories[$categoryId]->getName()) {
                $breadcrumb = array(
                    'label' => $categories[$categoryId]->getName(),
                    'link'  => ($categories[$categoryId]->getId()==$this->getCurrentCategory()->getId()) 
                        ? '' : Mage::getUrl('*/*/*', array('id'=>$categories[$categoryId]->getId()))
                );
                $this->getLayout()->getBlock('breadcrumbs')
                    ->addCrumb('category'.$categoryId, $breadcrumb);
            }
        }

        
        $this->getLayout()->getBlock('head')->setTitle($this->getCurrentCategory()->getName());            
    }
    
    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getProductCollection()
    {
        return Mage::getSingleton('catalog/layer')->getProductCollection();
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
