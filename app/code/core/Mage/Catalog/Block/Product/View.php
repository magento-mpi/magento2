<?php

/**
 * Product View block
 *
 * @package    Mage
 * @module     Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Block_Product_View extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/product/view.phtml');
        #$this->loadData(Mage::registry('controller')->getRequest());
    }

    public function loadData()
    {
        $request = Mage::registry('controller')->getRequest();
        $categoryId = $request->getParam('category', false);
        $productId = $request->getParam('id');
        $product = Mage::getModel('catalog', 'product')->load($productId)->setCategoryId($categoryId);
        
        $breadcrumbs = $this->getLayout()->createBlock('catalog_breadcrumbs', 'catalog.breadcrumbs');
        $breadcrumbs->addCrumb('home', array('label'=>__('Home'),'title'=>__('Go to home page'),'link'=>Mage::getBaseUrl()));
        $breadcrumbs->addCrumb('category', array('label'=>$product->getCategoryName(), 'title'=>'', 'link'=>$product->getCategoryUrl()));
        $breadcrumbs->addCrumb('product', array('label'=>$product->getName()));
        $this->setChild('breadcrumbs', $breadcrumbs);
        
        $currencyFilter = new Varien_Filter_Sprintf('$%s', 2);
        
        $this->assign('currencyFilter', $currencyFilter);
        $this->assign('product', $product);
        
        $prices = $product->getTierPrice();
        foreach ($prices as $index => $price) {
            
        }
        return $this;
    }
}