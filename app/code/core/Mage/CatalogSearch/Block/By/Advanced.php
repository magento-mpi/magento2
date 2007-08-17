<?php

class Mage_CatalogSearch_Block_By_Advanced extends Mage_Core_Block_Template
{

    public function loadByAdvancedSearch(Zend_Controller_Request_Http $request)
    {
        $this->setTemplate('catalog/search/result.phtml');
        $search = $request->getParam('search', array());
        $request->setParam('search', false);
        
        Mage::registry('action')->getLayout()->getBlock('head.meta')->setTitle('Advanced search results');

        $page = $request->getParam('p',1);
        $prodCollection = Mage::getResourceModel('catalog/product_collection')
            ->distinct(true)
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('description')
            ->setOrder($request->getParam('order','name'), $request->getParam('dir','asc'))
            ->setCurPage($page)
            ->setPageSize(9);
        
        if (!empty($search['query'])) {
            $prodCollection->addSearchFilter($search['query']);
        }
        if (!empty($search['category'])) {
            $prodCollection->addCategoryFilter($search['category']);
        }
        else {
            $prodCollection->addCategoryFilter($this->getArrCategoriesId());
        }
        if (!empty($search['price'])) {
            
        }
        if (!empty($search['type'])) {
            $prodCollection->addAttributeToSelect('type', $search['type']);
        }
        if (!empty($search['manufacturer'])) {
            $prodCollection->addAttributeToSelect('manufacturer', $search['manufacturer']);
        }
        
        $prodCollection->load();
        
        $this->assign('query', 'Advanced search');
        $this->assign('productCollection', $prodCollection);

        $pageUrl = clone $request;
        $pageUrl->setParam('array', array('search'=>$search));
        $this->assign('pageUrl', $pageUrl);
        
        $sortUrl = clone $request;
        $sortUrl->setParam('p', 1)->setParam('dir', 'asc');
        $sortUrl->setParam('array', array('search'=>$search));
        $this->assign('sortUrl', $sortUrl);
        
        $this->assign('sortValue', $request->getParam('order','name').'_'.$request->getParam('dir','asc'));
    }
    
}