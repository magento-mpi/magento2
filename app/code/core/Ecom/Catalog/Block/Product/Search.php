<?php

#include_once "Ecom/Core/Block/Template.php";

/**
 * Product search result block
 *
 * @package    Ecom
 * @module     Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Ecom_Catalog_Block_Product_Search extends Ecom_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setViewName('Ecom_Catalog', 'search.result');
    }

    public function loadData(Zend_Controller_Request_Http $request)
    {
        $query = $this->getAttribute('query');
        $queryEscaped = htmlspecialchars($query);
        $breadcrumbs = Ecom::createBlock('catalog_breadcrumbs', 'catalog.breadcrumbs');
        $breadcrumbs->addCrumb('home', array('label'=>'Home','title'=>'Go to home page','link'=>Ecom::getBaseUrl()));
        $breadcrumbs->addCrumb('query', array('label'=>$queryEscaped));
        $this->setChild('breadcrumbs', $breadcrumbs);

        Ecom::getBlock('head.title')->setContents('Search result for: '.$queryEscaped);

        $prodCollection = Ecom::getModel('catalog','product_collection');

        $prodCollection->addFilter('website_id', Ecom::getCurentWebsite(), 'and');
        $prodCollection->addSearchFilter($query);

        $page = $request->getParam('p',1);
        $prodCollection->setOrder($request->getParam('order','name'), $request->getParam('dir','asc'));
        $prodCollection->setCurPage($page);
        $prodCollection->loadData();

        $this->assign('query', $queryEscaped);
        $this->assign('productCollection', $prodCollection);

        $pageUrl = clone $request;
        $this->assign('pageUrl', $pageUrl);
        
        $sortUrl = clone $request;
        $sortUrl->setParam('p', 1)->setParam('dir', 'asc');
        $this->assign('sortUrl', $sortUrl);
        
        $this->assign('sortValue', $request->getParam('order','name').'_'.$request->getParam('dir','asc'));
    }
}

