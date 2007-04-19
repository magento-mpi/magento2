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
    public function __construct()
    {
        parent::__construct();
        $this->setViewName('Mage_Catalog', 'category/view.phtml');
    }

    public function loadData(Zend_Controller_Request_Http $request)
    {
        $category = $this->getAttribute('category');
        
        // Breadcrumbs
        $breadcrumbs = Mage::createBlock('catalog_breadcrumbs', 'catalog.breadcrumbs');
        $breadcrumbs->addCrumb('home', array('label'=>__('Home'),'title'=>__('Go to home page'),'link'=>Mage::getBaseUrl()));
        $breadcrumbs->addCrumb('category', array('label'=>$category->getName()));
        $this->setChild('breadcrumbs', $breadcrumbs);
        
        // get category filters
        $filters = $category->getFilters();
        // get filter values from request
        $filterValues = $request->getQuery('filter', array());
        // set values current values to all items
        $filters->walk('setCurrentValues', array($filterValues));
        // clear request param
        $request->setParam('filter', false);
        
        // Init collection
        $prodCollection = $category->getProductCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            // add filters
            ->addFrontFilters($filters->getItemsById(array_keys($filterValues)))
            ->setPageSize(9);
        
        // get avilable filter values from collection
        foreach ($filters as $filter) {
            $filter->setAvailableValues($prodCollection->getAttributeValues($filter->getAttributeId()));
        }
        // assign
        $this->assign('filters', $filters);
        
        Mage::getBlock('catalog.leftnav')->assign('currentCategoryId',$category->getId());

        $page = $request->getParam('p',1);
        $prodCollection->setOrder($request->getParam('order','name'), $request->getParam('dir','asc'));
        $prodCollection->setCurPage($page);
        $prodCollection->load();
        $prodCollection->walk('setCategoryId', $category->getId());

        $this->assign('category', $category);
        $this->assign('productCollection', $prodCollection);
        
        $pageUrl = clone $request;
        // set filter values
        $pageUrl->setParam('array', array('filter'=>$filterValues));
        $this->assign('pageUrl', $pageUrl);
        
        $sortUrl = clone $request;
        $sortUrl->setParam('array', array('filter'=>$filterValues));
        $sortUrl->setParam('p', 1)->setParam('dir', 'asc');
        
        $this->assign('sortUrl', $sortUrl);
        $this->assign('sortValue', $request->getParam('order','name').'_'.$request->getParam('dir','asc'));
    }
}