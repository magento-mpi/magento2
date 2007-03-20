<?php



/**
 * Product search result block
 *
 * @package    Mage
 * @module     Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Block_Product_Search extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setViewName('Mage_Catalog', 'search.result');
    }

    public function loadData(Zend_Controller_Request_Http $request)
    {
        $query = $this->getAttribute('query');
        $queryEscaped = htmlspecialchars($query);
        $breadcrumbs = Mage::createBlock('catalog_breadcrumbs', 'catalog.breadcrumbs');
        $breadcrumbs->addCrumb('home', array('label'=>'Home','title'=>'Go to home page','link'=>Mage::getBaseUrl()));
        $breadcrumbs->addCrumb('query', array('label'=>$queryEscaped));
        $this->setChild('breadcrumbs', $breadcrumbs);

        Mage::getBlock('head.title')->setContents('Search result for: '.$queryEscaped);

        $prodCollection = Mage::getModel('catalog','product_collection');

        $prodCollection->addFilter('website_id', Mage_Core_Environment::getCurentWebsite(), 'and');
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

