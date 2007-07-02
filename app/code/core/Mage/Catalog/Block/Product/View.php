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
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('catalog/product/view.phtml');
    }

    public function loadData()
    {
        $categoryId = $this->getRequest()->getParam('category', false);
        $productId  = $this->getRequest()->getParam('id');
        
        $product = Mage::getModel('catalog/product')
            ->load($productId)
            ->setCategoryId($categoryId);
        
        $breadcrumbs = $this->getLayout()
            ->createBlock('catalog/breadcrumbs', 'catalog.breadcrumbs');
        $breadcrumbs->addCrumb('home', 
            array('label'=>__('Home'), 'title'=>__('Go to home page'), 'link'=>Mage::getBaseUrl())
        );
        $breadcrumbs->addCrumb('category', 
            array('label'=>$product->getCategoryName(), 'title'=>'', 'link'=>$product->getCategoryUrl())
        );
        $breadcrumbs->addCrumb('product', 
            array('label'=>$product->getName())
        );
        
        $this->setChild('breadcrumbs', $breadcrumbs);
        
        $this->assign('product', $product);
        $this->assign('customerIsLogin', Mage::getSingleton('customer/session')->isLoggedIn());
        
        $this->assign('tags', $this->getLayout()->createBlock('tag/list')->toHtml());
        
        $this->assign('reviewCount', $this->getLayout()->createBlock('review/list')->count());
        $this->assign('reviewLink', Mage::getUrl('review/product/list', array('id'=>$productId)));
        $this->setChild('rating', $this->getLayout()->createBlock('rating/product'));
        $this->setChild('reviewForm', $this->getLayout()->createBlock('review/form'));
        
        return $this;
    }
}