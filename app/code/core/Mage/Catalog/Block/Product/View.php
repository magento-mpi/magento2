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
        	$storeId = (int) Mage::getSingleton('core/store')->getId();
        	$product = Mage::getModel('catalog/product')
        		->setStoreId($storeId)
            	->load($productId)
            	->setCategoryId($categoryId)
            	->setStoreId($storeId);

           	Mage::register('product', $product);
        }

        if($product->isBundle()) {
        	$product->getBundleOptionCollection()->useProductItem()->getLinkCollection()
        		->addAttributeToSelect('name')
        		->addAttributeToSelect('price');
        	$product->getBundleOptionCollection()
        		->load();
        }

        $product->getRelatedProducts()
			->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('image')
            ->addAttributeToSelect('small_image')
			->addAttributeToSort('position', 'asc')
			->useProductItem();

		$product->getSuperGroupProducts()
			->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('sku')
			->addAttributeToSort('position', 'asc')
			->useProductItem();

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

        $this->getLayout()->getBlock('root')->setHeaderTitle($product->getName());

        $this->assign('product', $product);
        $this->assign('customerIsLogin', Mage::getSingleton('customer/session')->isLoggedIn());

        $this->assign('tags', $this->getLayout()->createBlock('tag/list')->toHtml());

        $this->assign('reviewLink', Mage::getUrl('review/product/list', array('id'=>$productId)));
        $this->assign('wishlistLink', Mage::getUrl('wishlist/index/add', array('product'=>$productId)));
        $this->setChild('rating', $this->getLayout()->createBlock('rating/entity_summary')
            ->setEntityId($productId));
        $this->setChild('reviewForm', $this->getLayout()->createBlock('review/form'));
        $this->setChild('reviewList', $this->getLayout()->createBlock('review/list', 'review_list'));
        $this->assign('reviewCount', $this->getLayout()->getBlock('review_list')->count());

        return $this;
    }

    public function getCompareJsObjectName()
    {
    	if($this->getLayout()->getBlock('catalog.compare.sidebar')) {
    		return $this->getLayout()->getBlock('catalog.compare.sidebar')->getJsObjectName();
    	}

    	return false;
    }

    public function getPricingValue($value)
    {
    	$value = Mage::registry('product')->getPricingValue($value);
    	$numberSign = $value >= 0 ? '+' : '-';
    	return ' ' . $numberSign . ' ' . Mage::getSingleton('core/store')->formatPrice(abs($value));
    }
    
    public function getGalleryImages()
    {
        return Mage::registry('product')->getGallery();
    }

}