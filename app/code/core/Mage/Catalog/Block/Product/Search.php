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
    }

    public function getArrCategoriesId()
    {
        $arr = array();
        // TODO: dependent from store id
        $nodes = Mage::getResourceModel('catalog/category_tree')
            ->load(2,10) // TODO: from config
            ->getNodes();
        foreach ($nodes as $node) {
            $arr[] = $node->getId();
        }
        
        return $arr;
    }

    public function loadByQuery(Zend_Controller_Request_Http $request)
    {
        $this->setTemplate('catalog/search/result.phtml');
        $query = $this->getQuery();
        $queryEscaped = htmlspecialchars($query);

        Mage::registry('action')->getLayout()->getBlock('head')->setTitle('Search results for: '.$queryEscaped);

        $page = $request->getParam('p',1);
        $prodCollection = Mage::getResourceModel('catalog/product_collection')
//            ->distinct(true)
//            ->addCategoryFilter($this->getArrCategoriesId())
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('description')
            ->addAttributeToSelect('image')
            ->addAttributeToSelect('small_image')
//            ->addSearchFilter($query)
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
    
    public function loadByAttributeOption(Zend_Controller_Request_Http $request)
    {
        $this->setTemplate('catalog/search/attribute.phtml');
        
        $attribute = $request->getParam('attr');
        $attributeValue = $request->getParam('value');

        $page = $request->getParam('p',1);
        
        $prodCollection = Mage::getResourceModel('catalog/product_collection')
            ->distinct(true)
            ->addCategoryFilter($this->getArrCategoriesId())
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('description')
            ->addAttributeToSelect($attribute, $attributeValue)
            ->setOrder($request->getParam('order','name'), $request->getParam('dir','asc'))
            ->setCurPage($page)
            ->setPageSize(9)
            ->loadData();

        $this->assign('productCollection', $prodCollection);
        $this->assign('option', Mage::getModel('catalog/product_attribute_option')->load($attributeValue));

        $pageUrl = clone $request;
        $this->assign('pageUrl', $pageUrl);
        
        $sortUrl = clone $request;
        $sortUrl->setParam('p', 1)->setParam('dir', 'asc');
        $this->assign('sortUrl', $sortUrl);
        $this->assign('sortValue', $request->getParam('order','name').'_'.$request->getParam('dir','asc'));
    }
    
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
