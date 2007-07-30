<?php
/**
 * Catalog product
 *
 * @package    Mage
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @author     Ivan Chepurnyi <mitch@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Product extends Varien_Object 
{
	protected $_cachedLinkedProductsByType = array();
	protected $_linkedProductsForSave = array();
	protected $_attributes;
	
    public function __construct() 
    {
        parent::__construct();
        $this->setIdFieldName($this->getResource()->getEntityIdField());
    }
    
    /**
     * Retrieve product category id
     *
     * @return int
     */
    public function getCategoryId()
    {
        $categoryId = ($this->getData('category_id')) ? $this->getData('category_id') : $this->getDefaultCategory();
        return $categoryId;
    }
    
    /**
     * Retrieve product resource model
     *
     * @return mixed
     */
    public function getResource()
    {
        return Mage::getResourceSingleton('catalog/product');
    }
    
    /**
     * Load product
     *
     * @param   int $productId
     * @return  Mage_Catalog_Model_Product
     */
    public function load($productId)
    {
        $this->getResource()->load($this, $productId);
        return $this;
    }
    
    /**
     * Save product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function save()
    {
        $this->getResource()->save($this);
        return $this;
    }
    
    public function delete()
    {
        $this->getResource()->delete($this);
        return $this;
    }
    
    public function setStoreId($storeId)
    {
        $this->getResource()->setStore($storeId);
        $this->setData('store_id', $storeId);
        return $this;
    }
    
    /**
     * Get product tier price by qty
     *
     * @param   double $qty
     * @return  double
     */
    public function getTierPrice($qty=null)
    {
        $prices = $this->getData('tier_price');
        
        if (empty($prices) || !is_array($prices)) {
            if (!is_null($qty)) {
                return $this->getPrice();
            }
            return array(array('price'=>$this->getPrice(), 'price_qty'=>1));
        }
        
        if ($qty) {
            $prevQty = 1;
            $prevPrice = $this->getPrice();
            foreach ($prices as $price) {
                if (($prevQty <= $qty) && ($qty < $price['price_qty'])) {
                    return $prevPrice;
                }
                $prevPrice = $price['price'];
                $prevQty = $price['price_qty'];
            }
            return $prevPrice;
        }
        
        return ($prices) ? $prices : array();
    }
    
    /**
     * Get formated by currency tier price
     *
     * @param   double $qty
     * @return  array || double
     */
    public function getFormatedTierPrice($qty=null)
    {
        $price = $this->getTierPrice($qty);
        if (is_array($price)) {
            foreach ($price as $index => $value) {
                $price[$index]['price'] = Mage::getSingleton('core/store')->formatPrice($price[$index]['price']);
            }
        }
        else {
            $price = Mage::getSingleton('core/store')->formatPrice($price);
        }
        
        
        return $price;
    }

    public function getFormatedPrice()
    {
        return Mage::getSingleton('core/store')->formatPrice($this->getPrice());
    }
    
    public function getLinkedProducts($linkType)
    {
        if(!isset($this->_cachedLinkedProductsByType[$linkType])) {
	    	$this->_cachedLinkedProductsByType[$linkType] = Mage::getResourceModel('catalog/product_link_collection');
	    	$this->_cachedLinkedProductsByType[$linkType]
    	           	->setLinkType($linkType)
    	           	->setProductId($this->getId())
    	           	->setStoreId($this->getStoreId())
    	        	->addLinkTypeFilter()
    	            ->addProductFilter()
    	            ->addStoreFilter();
    	        
    		    $attibutes = $this->_cachedLinkedProductsByType[$linkType]->getLinkAttributeCollection();
    			foreach ($attibutes as $attibute) {
    				$this->_cachedLinkedProductsByType[$linkType]->addLinkAttributeToSelect($attibute->getCode());
    			}
        }
       
        return $this->_cachedLinkedProductsByType[$linkType];
    }
    
    public function getLinkedProductsLoaded($linkType)
    {
    	if(!$this->getLinkedProducts($linkType)->getIsLoaded()) {
    		$this->getLinkedProducts($linkType)->load();
    	}
    	
    	return $this->getLinkedProducts($linkType);
    }
    
    public function setLinkedProducts($linkType, array $linkAttibutes)
    {
    	$this->addLinkedProductsForSave($linkType, $linkAttibutes);
    	      	
        return $this;
    }
    
    public function addLinkedProductsForSave($linkType, array $data) 
    {
    	$this->_linkedProductsForSave[$linkType] = $data;
    	return $this;
    }
    
    public function getLinkedProductsForSave()
    {
    	return $this->_linkedProductsForSave;
    }
    
    public function setRelatedProducts(array $linkAttibutes)
    {
        return $this->setLinkedProducts('relation', $linkAttibutes);
    }
    
    public function getRelatedProducts()
    {
        return $this->getLinkedProducts('relation');
    }
    
    public function getRelatedProductsLoaded()
    {
        return $this->getLinkedProductsLoaded('relation');
    }
    
    public function setUpSellProducts(array $linkAttibutes)
    {
        return $this->setLinkedProducts('up_sell', $linkAttibutes);
    }
    
    public function getUpSellProducts()
    {
        return $this->getLinkedProducts('up_sell');
    }
    
    public function getUpSellProductsLoaded()
    {
        return $this->getLinkedProductsLoaded('up_sell');
    }
    
    public function setCrossSellProducts(array $linkAttibutes)
    {
        return $this->setLinkedProducts('cross_sell', $linkAttibutes);
    }
    
    public function getCrossSellProducts()
    {
        return $this->getLinkedProducts('cross_sell');
    }
     
    public function getCrossSellProductsLoaded()
    {
        return $this->getLinkedProductsLoaded('cross_sell');
    }
    
    /**
     * Retrieve product categories
     *
     * @return Varien_Data_Collection
     */
    public function getCategoryCollection()
    {
        $collection = $this->getResource()->getCategoryCollection($this);
        return $collection;
    }
    
    /**
     * Retrieve product store Ids array
     *
     * @return array
     */
    public function getStoreIds()
    {
        $storeIds = $this->getData('store_ids');
        if (is_null($storeIds)) {
            $storeIds = $this->getResource()->getStoreIds($this);
            $this->setData('store_ids', $storeIds);
        }
        return $storeIds;
    }
    
    /**
     * Retrieve product stores collection
     *
     * @return unknown
     */
    public function getStoreCollection()
    {
        $collection = $this->getResource()->getStoreCollection($this);
        return $collection;
    }
    
    /**
     * Retrieve product attributes
     * 
     * if $groupId is null - retrieve all product attributes
     * 
     * @param   int $groupId
     * @return  array
     */
    public function getAttributes($groupId = null)
    {
        if (!$this->_attributes) {
            $this->_attributes = $this->getResource()
                ->loadAllAttributes()
                ->getAttributesByName();
        }
        
        if (is_null($groupId)) {
            return $this->_attributes;
        }
        
        $attributes = array();
        foreach ($this->_attributes as $attribute) {
        	if ($attribute->getAttributeGroupId() == $groupId) {
        	    $attributes[] = $attribute;
        	}
        }
        return $attributes;
    }
    
    ///////////////////////////////////////////////////
    /// need remove
    ////////////////////
    /**
     * Get product url
     *
     * @return string
     */
    public function getProductUrl()
    {
        $url = Mage::getUrl('catalog/product/view', 
            array(
                'id'=>$this->getId(),
                'category'=>$this->getCategoryId()
            ));
        return $url;
    }
    
    /**
     * Get product category url
     *
     * @return string
     */
    public function getCategoryUrl()
    {
        $url = Mage::getUrl('catalog/category/view', array('id'=>$this->getCategoryId()));
        return $url;
    }
    
    public function getImageUrl()
    {
        #$url = Mage::getBaseUrl(array('_admin'=>false, '_type'=>'media')).'catalog/product/'.($this->getProductId()%977).'/'.$this->getProductId().'.orig.'.$this->getImage();
        $url = Mage::getBaseUrl(array('_admin'=>false, '_type'=>'media')).$this->getImage();
        return $url;
    }
        
    public function getSmallImageUrl()
    {
        #$url = Mage::getBaseUrl(array('_admin'=>false, '_type'=>'media')).'catalog/product/'.($this->getProductId()%977).'/'.$this->getProductId().'.orig.'.$this->getImage();
        $url = Mage::getBaseUrl(array('_admin'=>false, '_type'=>'media')).$this->getSmallImage();
        return $url;
    }
    
    /**
     * Get product category name
     *
     * @return unknown
     */
    public function getCategoryName()
    {
        return 'node';//Mage::getResourceModel('catalog/category_tree')->joinAttribute('name')->loadNode($this->getCategoryId())->getName();
    }
    
}