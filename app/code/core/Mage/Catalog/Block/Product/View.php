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
    }

    public function loadData()
    {
        $categoryId = $this->getRequest()->getParam('category', false);
        $productId  = $this->getRequest()->getParam('id');

        if(!$product = Mage::registry('product')) {
        	$product = Mage::getModel('catalog/product')
            	->load($productId)
            	->setCategoryId($categoryId);
           	Mage::register('product', $product);
        }

        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        $breadcrumbs->addCrumb('home',
            array('label'=>__('Home'), 'title'=>__('Go to Home Page'), 'link'=>Mage::getBaseUrl())
        );
        $breadcrumbs->addCrumb('category',
            array('label'=>$product->getCategoryName(), 'title'=>'', 'link'=>$product->getCategoryUrl())
        );
        $breadcrumbs->addCrumb('product',
            array('label'=>$product->getName())
        );

        //$this->setChild('breadcrumbs', $breadcrumbs);

        $this->getLayout()->getBlock('root')->setHeaderTitle($product->getName());

        $this->assign('product', $product);
        $this->assign('customerIsLogin', Mage::getSingleton('customer/session')->isLoggedIn());

        $this->assign('tags', $this->getLayout()->createBlock('tag/list')->toHtml());

        $this->assign('reviewCount', $this->getLayout()->createBlock('review/list')->count());
        $this->assign('reviewLink', Mage::getUrl('review/product/list', array('id'=>$productId)));
        $this->assign('wishlistLink', Mage::getUrl('wishlist/index/add', array('product'=>$productId)));
        $this->setChild('rating', $this->getLayout()->createBlock('rating/product'));
        $this->setChild('reviewForm', $this->getLayout()->createBlock('review/form'));

        return $this;
    }

    public function getCompareJsObjectName()
    {
    	if($this->getLayout()->getBlock('catalog.compare.sidebar')) {
    		return $this->getLayout()->getBlock('catalog.compare.sidebar')->getJsObjectName();
    	}

    	return false;
    }

    public function getGalleryImages()
    {
        return Mage::registry('product')->getGallery();
    }

}
