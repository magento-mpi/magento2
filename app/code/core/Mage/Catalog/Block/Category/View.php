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

    private $breadcrumbs;
    /**
     * Product Collection
     *
     * @var Mage_Catalog_Model_Mysql4_Product_Collection
     */
    private $prodCollection;
    private $currentCategory;

    public function __construct()
    {
        parent::__construct();
        $this->setViewName('Mage_Catalog', 'category/view');
    }

    public function loadData(Zend_Controller_Request_Http $request)
    {
        $this->currentCategory = $this->getAttribute('category');

        $breadcrumbs = Mage::createBlock('catalog_breadcrumbs', 'catalog.breadcrumbs');
        $breadcrumbs->addCrumb('home', array('label'=>'Home','title'=>'Go to home page','link'=>Mage::getBaseUrl().'/'));
        $breadcrumbs->addCrumb('category', array('label'=>$this->currentCategory->getData('name')));
        $this->setChild('breadcrumbs', $breadcrumbs);

        $this->prodCollection = Mage::getModel('catalog','product_collection');

        $this->prodCollection->addFilter('website_id', Mage::getCurentWebsite(), 'and');
        $this->prodCollection->addFilter('category_id', $this->currentCategory->getId() , 'and');

        Mage::getBlock('catalog.leftnav.bytopic')->assign('currentCategoryId',$this->currentCategory->getId());
        Mage::getBlock('catalog.leftnav.byproduct')->assign('currentCategoryId',$this->currentCategory->getId());

        $page = $request->getParam('p',1);
        $this->prodCollection->setOrder($request->getParam('order','name'), $request->getParam('dir','asc'));
        $this->prodCollection->setCurPage($page);
        $this->prodCollection->loadData();

        $this->assign('category', $this->currentCategory);
        $this->assign('productCollection', $this->prodCollection);
        
        $pageUrl = clone $request;
        $this->assign('pageUrl', $pageUrl);
        
        $sortUrl = clone $request;
        $sortUrl->setParam('p', 1)->setParam('dir', 'asc');
        $this->assign('sortUrl', $sortUrl);
        
        $this->assign('sortValue', $request->getParam('order','name').'_'.$request->getParam('dir','asc'));
    }
}