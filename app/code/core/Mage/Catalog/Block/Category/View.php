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
     * Retrieve current category model object
     *
     * @return Mage_Catalog_Model_Category
     */
    public function getCurrentCategory()
    {
        return Mage::registry('current_category');
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
    
    public function getCanShowName()
    {
        return $this->getCurrentCategory()->getDisplayMode()!=Mage_Catalog_Model_Category::DM_MIXED;
    }
}
