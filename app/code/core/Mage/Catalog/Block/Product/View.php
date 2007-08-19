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
        $product = $this->getProduct();
        
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
        /*$breadcrumbs->addCrumb('category',
            array('label'=>$product->getCategoryName(), 'title'=>'', 'link'=>$product->getCategoryUrl())
        );*/
        $breadcrumbs->addCrumb('product',
            array('label'=>$product->getName())
        );

        $this->getLayout()->getBlock('root')->setHeaderTitle($product->getName());

        $this->assign('product', $product);
        $this->assign('customerIsLogin', Mage::getSingleton('customer/session')->isLoggedIn());

        $this->assign('reviewLink', Mage::getUrl('review/product/list', array('id'=>$product->getId())));
        $this->assign('wishlistLink', Mage::getUrl('wishlist/index/add', array('product'=>$product->getId())));
        $this->setChild('rating', $this->getLayout()->createBlock('rating/entity_summary')
            ->setEntityId($product->getId()));
        $this->setChild('reviewForm', $this->getLayout()->createBlock('review/form'));
        $this->setChild('reviewList', $this->getLayout()->createBlock('review/list', 'review_list'));

        $this->setChild('tagList', $this->getLayout()->createBlock('tag/product_list'));

        $this->assign('reviewCount', $this->getLayout()->getBlock('review_list')->count());

        return $this;
    }

    /**
     * Retrieve current product model
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('product');
    }
    
    public function getAdditionalData()
    {
        $data = array();
        $product = $this->getProduct();
        $attributes = $product->getAttributes();
        foreach ($attributes as $attribute) {
        	if ($attribute->getIsVisibleOnFront() && $attribute->getIsUserDefined()) {
        	    $value = $attribute->getFrontend()->getValue($product);
        	    if (strlen($value)) {
            	    $data[$attribute->getAttributeCode()] = array(
            	       'label' => __($attribute->getFrontend()->getLabel()),
            	       'value' => $attribute->getFrontend()->getValue($product)//$product->getData($attribute->getAttributeCode())
            	    );
        	    }
        	}
        }
        return $data;
    }
    
    /**
     * URLs section
     */
    
    public function getReviewUrl()
    {
        
    }
    
    public function getAddToWishlistUrl()
    {
        
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
        $collection = $this->getProduct()->getGallery();
        return $collection;
    }
    
    public function getGalleryUrl($image=null)
    {
        $params = array('id'=>$this->getProduct()->getId());
        if ($image) {
            $params['image'] = $image->getValueId();
            return $this->getUrl('*/*/gallery', $params);
        }
        return $this->getUrl('*/*/gallery', $params);
    }
}