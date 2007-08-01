<?php

/**
 * Category View block
 *
 * @package    Mage
 * @module     Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Block_Category_View extends Mage_Core_Block_Template
{
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
        
        // Init breadcrumbs
        $this->getLayout()->getBlock('breadcrumbs')
            ->addCrumb('home',
                array('label'=>__('Home'),
                    'title'=>__('Go to Home Page'),
                    'link'=>Mage::getBaseUrl())
                )
            ->addCrumb('category',
                array('label'=>$this->getCurrentCategory()->getName())
            );
        
        $this->getLayout()->getBlock('head')->setTitle($this->getCurrentCategory()->getName());            
    }
    
    protected function _getProductCollection()
    {
        if (!$this->_productCollection) {
            $request = $this->getRequest();
            $store   = Mage::getSingleton('core/store');
            
            $this->_productCollection = $this->getCurrentCategory()->getProductCollection()
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('price')
                ->addAttributeToSelect('image')
                ->addAttributeToSelect('small_image')
                ->addAttributeToSelect('description')
                ->setOrder($request->getParam('order','name'), $request->getParam('dir','asc'))
                ->setCurPage($request->getParam('p', 1))
                ->setPageSize($request->getParam('limit', 9))
                ->joinField('store_id', 
                    'catalog/product_store', 
                    'store_id', 
                    'product_id=entity_id', 
                    '{{table}}.store_id='.(int) $store->getId())
                ->joinField('position', 
                    'catalog/category_product', 
                    'position', 
                    'product_id=entity_id', 
                    'category_id='.(int) $this->getCurrentCategory()->getId());
                    
            $this->_productCollection->getEntity()->setStore($store->getId());
            $this->_productCollection->load();
        }
        return $this->_productCollection;
    }
    
    public function getCurrentCategory()
    {
        return Mage::registry('current_category');
    }
    
    public function getLoadedProductCollection()
    {
        return $this->_getProductCollection();
    }
    
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    
    public function getMode()
    {
        return $this->getRequest()->getParam('mode');
    }
    
    public function getCompareJsObjectName()
    {
    	if($this->getLayout()->getBlock('catalog.compare.sidebar')) {
    		return $this->getLayout()->getBlock('catalog.compare.sidebar')->getJsObjectName();
    	} 
    	
    	return false;
    }
}
