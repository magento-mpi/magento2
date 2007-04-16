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

    public function loadByQuery(Zend_Controller_Request_Http $request)
    {
        $this->setViewName('Mage_Catalog', 'search.result.phtml');
        $query = $this->getAttribute('query');
        $queryEscaped = htmlspecialchars($query);

        Mage::getBlock('head.title')->setContents('Search result for: '.$queryEscaped);

        $page = $request->getParam('p',1);
        $prodCollection = Mage::getModel('catalog','product_collection')
            ->addAttributeToSelect('name', 'varchar')
            ->addAttributeToSelect('price', 'decimal')
            ->addAttributeToSelect('description', 'text')
            ->addSearchFilter($query)
            ->setOrder($request->getParam('order','name'), $request->getParam('dir','asc'))
            ->setCurPage($page)
            ->loadData();

        $this->assign('query', $queryEscaped);
        $this->assign('productCollection', $prodCollection);

        $pageUrl = clone $request;
        $this->assign('pageUrl', $pageUrl);
        
        $sortUrl = clone $request;
        $sortUrl->setParam('p', 1)->setParam('dir', 'asc');
        $this->assign('sortUrl', $sortUrl);
        
        $this->assign('sortValue', $request->getParam('order','name').'_'.$request->getParam('dir','asc'));
    }
    
    public function loadByAttribute(Zend_Controller_Request_Http $request)
    {
        $this->setViewName('Mage_Catalog', 'search.attribute.phtml');
        
        $attribute = $request->getParam('attr');
        $attributeValue = $request->getParam('value');


        $page = $request->getParam('p',1);
        $prodCollection = Mage::getModel('catalog','product_collection')
            ->addAttributeToSelect('name', 'varchar')
            ->addAttributeToSelect('price', 'decimal')
            ->addAttributeToSelect('description', 'text')
            ->addAttributeToSelect($attribute, 'int', $attributeValue)
            //->addSearchFilter($query)
            ->setOrder($request->getParam('order','name'), $request->getParam('dir','asc'))
            ->setCurPage($page)
            ->loadData();

        $this->assign('productCollection', $prodCollection);
        $this->assign('optionName', Mage::getModel('catalog', 'product_attribute_option')->getOptionValue($attributeValue));

        $pageUrl = clone $request;
        $this->assign('pageUrl', $pageUrl);
        
        $sortUrl = clone $request;
        $sortUrl->setParam('p', 1)->setParam('dir', 'asc');
        $this->assign('sortUrl', $sortUrl);
        $this->assign('sortValue', $request->getParam('order','name').'_'.$request->getParam('dir','asc'));
    }
}

