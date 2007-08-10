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
	
	/**
	 * Bundle products option collection
	 *
	 * @var Mage_Core_Model_Mysql4_Collection_Abstract
	 */
	protected $_bundleOptionCollection = null;
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
     * Count how many tier prices we have for the product
     *
     * @return  int
     */
    public function getTierPriceCount()
    {
        $price = $this->getTierPrice();
        return count($price);
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
    
    public function getFinalPrice()
    {
        $finalPrice = $this->getPrice();
        if (is_numeric($this->getTierPrice())) {
            $finalPrice = min($finalPrice, $this->getTierPrice());
        }
        if (is_numeric($this->getSpecialPrice())) {
            $finalPrice = min($finalPrice, $this->getSpecialPrice());
        }
        $this->setFinalPrice($finalPrice);
        
        Mage::dispatchEvent('catalog_product_get_final_price', array('product'=>$this));
        
        return $this->getData('final_price');
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
    
    public function setSuperGroupProducts(array $linkAttibutes)
    {
        return $this->setLinkedProducts('super', $linkAttibutes);
    }
    
    public function getSuperGroupProducts()
    {
        return $this->getLinkedProducts('super');
    }
     
    public function getSuperGroupProductsLoaded()
    {
    	if(!$this->getSuperGroupProducts()->getIsLoaded()) {
    		$this->getSuperGroupProducts()->load();
    	}
        return $this->getSuperGroupProducts();
    }
    
    public function getSuperAttributesIds()
    {
    	return $this->getData('super_attributes_ids');
    }
    
    public function setSuperAttributesIds(array $attributesIds)
    {
    	$resultAttributesIds = array();
    	foreach ($attributesIds as $attributeId) {
    		foreach ($this->getAttributes() as $attribute) {
    			if($attribute->getAttributeId()==$attributeId 
    				&& !$attribute->getIsRequired() 
    				&& $attribute->getIsGlobal() 
    				&& $attribute->getIsVisible() 
    				&& $attribute->getIsUserDefined() 
    				&& ($attribute->getSourceModel() || $attribute->getBackendType()=='int' )) {
    				$resultAttributesIds[] = $attributeId;		
    			}
    		}
    	}
    	
    	if(count($resultAttributesIds)>0) {
    		$this->setData('super_attributes_ids', $resultAttributesIds);
    	} else {
    		$this->setData('super_attributes_ids', null);
    	}
    	    	
    	return $this;
    }
    
    public function getSuperAttributes($asObject=false)
    {
    	if(!$this->getId()) {
    		$result = array();
    		$position = 0;
    		foreach ($this->getAttributes() as $attribute) {
    			if(in_array($attribute->getAttributeId(), $this->getSuperAttributesIds())) {
    				if(!$asObject) {
						$row = $attribute->toArray(array('label','attribute_id','attribute_code','id','frontend_label'));
						$row['values'] = array();
						$row['label'] = $row['frontend_label'];
						$row['position'] = $position++;
    				} else {
    					$row = $attribute;
    				}    				
    				$result[] = $row;
    			}
    		}
    		return $result;
    	} else {
    		// Implement
    	}
    }
    
    public function isBundle() 
    {
    	// TODO: use string value
    	return $this->getTypeId() == 2;
    }
    
    public function isSuperGroup() 
    {
    	// TODO: use string value
    	return $this->getTypeId() == 4;
    }
    
    public function isSuperConfig() 
    {
    	// TODO: use string value
    	return $this->getTypeId() == 3;
    }
    
    public function isAviableBundle() 
    {
    	foreach ($this->getBundleOptionCollection() as $bundleOption) {
    		if(sizeof($bundleOption->getLinkCollection()->getItems())==0) {
    			return false;
    		}
    	}
    	
    	return true;
    }
    
    public function getBundleOptionCollection()
    {
    	if(!$this->isBundle()) {
    		return false;
    	}
    	
    	if(is_null($this->_bundleOptionCollection)) {
    		$this->_bundleOptionCollection = $this->getResource()->getBundleOptionCollection($this);
    	}
    	
    	return $this->_bundleOptionCollection;
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
    public function getAttributes($groupId = null, $skipSuper=false)
    {
        if (!$this->_attributes) {
            $this->_attributes = $this->getResource()
                ->loadAllAttributes($this)
                ->getAttributesByCode();
        }
        
        if (is_null($groupId)) {
            return $this->_attributes;
        }
        
        $attributes = array();
        foreach ($this->_attributes as $attribute) {
        	if ($attribute->getAttributeGroupId() == $groupId 
        		// Skip super product attributes
        		&& (!$skipSuper || ! $this->getSuperAttributesIds() || !in_array($attribute->getAttributeId(), $this->getSuperAttributesIds()))) {
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
