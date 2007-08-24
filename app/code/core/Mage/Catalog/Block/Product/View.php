<?php

/**
 * Product View block
 *
 * @package    Mage
 * @module     Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Block_Product_View extends Mage_Catalog_Block_Product_Abstract
{

    protected function _construct()
    {
        parent::_construct();
    }
    
    protected function _initChildren()
    {
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            if ($title = $this->getProduct()->getMetaTitle()) {
                $headBlock->setTitle($title);
            }
            else {
                $headBlock->setTitle($this->getProduct()->getName());
            }
            
            if ($keyword = $this->getProduct()->getMetaKeyword()) {
                $headBlock->setKeywords($keyword);
            }
            if ($description = $this->getProduct()->getMetaDescription()) {
                $headBlock->setDescription($description);
            }            
        }
        
        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbsBlock->addCrumb('home',
                array('label'=>__('Home'), 'title'=>__('Go to Home Page'), 'link'=>Mage::getBaseUrl())
            );
            
            if ($category = $this->getProduct()->getCategory()) {
                $breadcrumbsBlock->addCrumb('category',
                    array('label'=>$category->getName(), 'title'=>'', 'link'=>$category->getCategoryUrl())
                );  
            }
            
            $breadcrumbsBlock->addCrumb('product',
                array('label'=>$this->getProduct()->getName())
            );
        }
    }
    
    protected function _beforeToHtml()
    {
        $this->_prepareData();
        return parent::_beforeToHtml();
    }

    protected function _prepareData()
    {
        $product = $this->getProduct();

        /*if($product->isBundle()) {
        	$product->getBundleOptionCollection()->useProductItem()->getLinkCollection()
        		->addAttributeToSelect('name')
        		->addAttributeToSelect('price');
        	$product->getBundleOptionCollection()
        		->load();
        }*/

		$product->getSuperGroupProducts()
			->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('sku')
			->addAttributeToSort('position', 'asc')
			->useProductItem();
			
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