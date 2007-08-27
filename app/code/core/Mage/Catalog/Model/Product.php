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
    /**
     * Product Types
     */
    const TYPE_SIMPLE               = 1;
    const TYPE_BUNDLE               = 2;
    const TYPE_CONFIGURABLE_SUPER   = 3;
    const TYPE_GROUPED_SUPER        = 4;
    
    const STATUS_ENABLED            = 1;
    const STATUS_DISABLED           = 2;
    const STATUS_OUT_OF_STOCK       = 3;
    
	protected $_cachedLinkedProductsByType = array();
	protected $_linkedProductsForSave = array();

	/**
	 * Bundle products option collection
	 *
	 * @var Mage_Core_Model_Mysql4_Collection_Abstract
	 */
	protected $_bundleOptionCollection = null;

	/**
	 * Super product attribute collection
	 *
	 * @var Mage_Core_Model_Mysql4_Collection_Abstract
	 */
	protected $_superAttributeCollection = null;

	/**
	 * Super product links collection
	 *
	 * @var Mage_Eav_Model_Mysql4_Entity_Collection_Abstract
	 */
	protected $_superLinkCollection = null;

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
        if ($category = Mage::registry('current_category')) {
            return $category->getId();
        }
        return false;
    }
    
    public function getCategory()
    {
        $category = $this->getData('category');
    	if (is_null($category) && $this->getCategoryId()) {
    	    $category = Mage::getModel('catalog/category')->load($this->getCategoryId());
    		$this->setCategory($category);
    	}
    	return $category;
    }

    /**
     * Retrieve product resource model
     *
     * @return Mage_Eav_Model_Entity_Abstract
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

    /**
     * Delete product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function delete()
    {
        $this->getResource()->delete($this);
        return $this;
    }
    
    public function copy()
    {
        $this->getResource()->copy($this);
        return $this;
    }

    /**
     * Product model validation
     *
     * @return Mage_Catalog_Model_Product
     */
    public function validate()
    {
        $this->getResource()->validate($this);
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
        
        /**
         * Load tier price
         */
        if (is_null($prices)) {
            if ($attribute = $this->getResource()->getAttribute('tier_price')) {
                $attribute->getBackend()->afterLoad($this);
                $prices = $this->getData('tier_price');
            }
        }

        if (is_null($prices) || !is_array($prices)) {
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
        return Mage::getSingleton('core/store')->formatPrice($this->getFinalPrice());
    }

    public function getFinalPrice($qty=null)
    {
        /**
         * Calculating final price for item of configurable product
         */
        if($this->getSuperProduct() && $this->getSuperProduct()->isSuperConfig()) {
        	$finalPrice = $this->getSuperProduct()->getFinalPrice($qty);
        	foreach ($this->getSuperProduct()->getSuperAttributes() as $attribute) {
        		if($value = $this->_getValueByIndex($attribute['values'], $this->getData($attribute['attribute_code']))) {
        			if($value['pricing_value'] != 0) {
        				$finalPrice += $this->getSuperProduct()->getPricingValue($value);
        			}
        		}
        	}
        } 
        /**
         * Calculating final price of simple product 
         */
        else {
        	$finalPrice = $this->getPrice();
        	$tierPrice  = $this->getTierPrice($qty);
	        if (is_numeric($tierPrice)) {
	            $finalPrice = min($finalPrice, $tierPrice);
	        }
	        if (is_numeric($this->getSpecialPrice())) {
	            $finalPrice = min($finalPrice, $this->getSpecialPrice());
	        }
        }

        $this->setFinalPrice($finalPrice);
        Mage::dispatchEvent('catalog_product_get_final_price', array('product'=>$this));
        return $this->getData('final_price');
    }

    public function getCalculatedPrice(array $options)
    {
    	$price = $this->getPrice();
    	foreach ($this->getSuperAttributes() as $attribute) {
    		if(isset($options[$attribute['attribute_id']])) {
	    		if($value = $this->_getValueByIndex($attribute['values'], $options[$attribute['attribute_id']])) {
	    			if($value['pricing_value'] != 0) {
	    				$price += $this->getPricingValue($value);
	    			}
	    		}
    		}
    	}
    	return $price;
    }

    protected function _getValueByIndex($values, $index) {
    	foreach ($values as $value) {
    		if($value['value_index'] == $index) {
    			return $value;
    		}
    	}
    	return false;
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
    	if(!$this->getData('super_attributes_ids') && $this->getId() && $this->isSuperConfig()) {
    		$superAttributesIds = array();
    		$superAttributes = $this->getSuperAttributes(true);
    		foreach ($superAttributes as $superAttribute) {
    			$superAttributesIds[] = $superAttribute->getAttributeId();
    		}
    		$this->setData('super_attributes_ids', $superAttributesIds);
    	}

    	return $this->getData('super_attributes_ids');
    }
    
    /**
     * Checkin attribute availability for superproduct
     *
     * @param   Mage_Eav_Model_Entity_Attribute $attribute
     * @return  bool
     */
    public function canUseAttributeForSuperProduct(Mage_Eav_Model_Entity_Attribute $attribute)
    {
        return $attribute->getIsGlobal()
            && $attribute->getIsRequired()
            && $attribute->getIsVisible()
            && $attribute->getIsUserDefined()
            && ($attribute->getSourceModel() || $attribute->getBackendType()=='int' );
    }
    
    public function setSuperAttributesIds(array $attributesIds)
    {
    	$resultAttributesIds = array();
		foreach ($this->getAttributes() as $attribute) {
			if(in_array($attribute->getAttributeId(), $attributesIds) && $this->canUseAttributeForSuperProduct($attribute)) {
				$resultAttributesIds[] = $attribute->getAttributeId();
			}
		}

    	if(count($resultAttributesIds)>0) {
    		$this->setData('super_attributes_ids', $resultAttributesIds);
    	} else {
    		$this->setData('super_attributes_ids', null);
    	}

    	return $this;
    }

    public function getSuperAttributes($asObject=false, $useLinkFilter=false)
    {
    	return $this->getResource()->getSuperAttributes($this, $asObject, $useLinkFilter);
    }

    public function setSuperAttributes(array $superAttributes)
    {
    	$this->setSuperAttributesForSave($superAttributes);
    	return $this;
    }

    public function getSuperLinks()
    {
    	return $this->getResource()->getSuperLinks($this);
    }

    public function getSuperLinkIdByOptions(array $options = null)
    {
    	if(is_null($options)) {
    		return false;
    	}

    	foreach ($this->getSuperLinks() as $linkId=>$linkAttributes) {
    		$have_it = true;
    		foreach ($linkAttributes as $attribute) {
    			if(isset($options[$attribute['attribute_id']]) && $options[$attribute['attribute_id']]!=$attribute['value_index']) {
    				$have_it = false;
    			}
    		}
    		if($have_it) {
    			return $linkId;
    		}
    	}

    	return false;
    }

    public function setSuperLinks(array $superLinks)
    {
    	$this->setSuperLinksForSave($superLinks);
    	return $this;
    }

    public function getSuperAttributesForSave()
    {
    	if(!$this->getData('super_attributes_for_save') && strlen($this->getBaseStoreId())>0 && $this->getId()) {
    		return $this->getSuperAttributes(false);
    	}

    	return $this->getData('super_attributes_for_save');
    }

    public function getSuperLinksForSave()
    {
    	if(!$this->getData('super_links_for_save') && strlen($this->getBaseStoreId())>0 && $this->getId()) {
    		return $this->getSuperLinks();
    	}

    	return $this->getData('super_links_for_save') ? $this->getData('super_links_for_save') : array();
    }

    public function getPricingValue($value)
    {
    	if($value['is_percent']) {
    		$ratio = $value['pricing_value']/100;
    		$price = $this->getPrice() * $ratio;
    	} else {
    		$price = $value['pricing_value'];
    	}

    	return $price;
    }

    public function isBundle()
    {
    	return $this->getTypeId() == self::TYPE_BUNDLE;
    }

    public function isSuperGroup()
    {
    	return $this->getTypeId() == self::TYPE_GROUPED_SUPER;
    }

    public function isSuperConfig()
    {
    	return $this->isConfigurable();
    }
    
    public function isConfigurable()
    {
        return $this->getTypeId() == self::TYPE_CONFIGURABLE_SUPER;
    }
    
    public function isSuper()
    {
        return $this->isSuperConfig() || $this->isSuperGroup();
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

    public function getSuperAttributeCollection()
    {
    	if(!$this->isSuperConfig())	{
    		return false;
    	}

    	if(is_null($this->_superAttributeCollection)) {
    		$this->_superAttributeCollection = $this->getResource()->getSuperAttributeCollection($this);
    	}

    	return $this->_superAttributeCollection;
    }

    public function getSuperAttributeCollectionLoaded()
    {
    	if(!$this->getSuperAttributeCollection()->getIsLoaded()) {
    		$this->getSuperAttributeCollection()->load();
    	}

    	return $this->getSuperAttributeCollection();
    }

    public function getSuperLinkCollection()
    {
    	if(!$this->isSuperConfig())	{
    		return false;
    	}

    	if(is_null($this->_superLinkCollection)) {
    		$this->_superLinkCollection = $this->getResource()->getSuperLinkCollection($this);
    	}

    	return $this->_superLinkCollection;
    }

    public function getSuperLinkCollectionLoaded()
    {
    	if(!$this->getSuperLinkCollection()->getIsLoaded()) {
    		$this->getSuperLinkCollection()->load();
    	}

    	return $this->getSuperLinkCollection();
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
        
        $attributes = array();
        if ($groupId) {
            foreach ($this->_attributes as $attribute) {
                // Remove attributes to uses in superproduct
                if ($this->isSuper()) {
                    if (!$attribute->getUseInSuperProduct()) {
                        continue;
                    }
                    if ($this->getSuperAttributesIds() && in_array($attribute->getAttributeId(), $this->getSuperAttributesIds())) {
                        continue;
                    }
                }
            	if ($attribute->getAttributeGroupId() != $groupId) {
            	    continue;
            	}
            	$attributes[] = $attribute;
            }
        }
        else {
            $attributes = $this->_attributes;
        }
        

        /*foreach ($this->_attributes as $attribute) {
        	if ($attribute->getAttributeGroupId() == $groupId
        		// Skip super product attributes
        		&& (!$skipSuper || ! $this->getSuperAttributesIds() || !in_array($attribute->getAttributeId(), $this->getSuperAttributesIds()))) {
        		$attributes[] = $attribute;
        	}
        }*/
        return $attributes;
    }

    /**
     * Get product url
     *
     * @return string
     */
    public function getProductUrl()
    {
    	$urlKey = $this->getUrlKey() ? $this->getUrlKey() : $this->formatUrlKey($this->getName());
        $url = Mage::getUrl('catalog/product/view',
            array(
            	's'=>$urlKey,
                'id'=>$this->getId(),
                'category'=>$this->getCategoryId()
            ));
        return $url;
    }
    
    public function formatUrlKey($str)
    {
    	$urlKey = preg_replace('#[^0-9a-z]+#i', '-', $str);
    	$urlKey = strtolower($urlKey);
    	$urlKey = trim($urlKey, '-');
    	
    	return $urlKey;
    }

    public function getImageUrl()
    {
        //$url = Mage::getBaseUrl(array('_admin'=>false, '_type'=>'media')).$this->getImage();
        $url = false;
        if ($attribute = $this->getResource()->getAttribute('image')) {
            $url = $attribute->getFrontend()->getUrl($this);
        }
        return $url;
    }

    public function getSmallImageUrl()
    {
        //$url = Mage::getBaseUrl(array('_admin'=>false, '_type'=>'media')).$this->getSmallImage();
        $url = false;
        if ($attribute = $this->getResource()->getAttribute('small_image')) {
            $url = $attribute->getFrontend()->getUrl($this);
        }
        return $url;
    }
    

    public function getThumbnailUrl()
    {
        $url = false;
        if ($attribute = $this->getResource()->getAttribute('thumbnail')) {
            $url = $attribute->getFrontend()->getUrl($this);
        }
        return $url;
    }
    
    public function getVisibleInCatalogStatuses()
    {
        return array(self::STATUS_ENABLED, self::STATUS_OUT_OF_STOCK);
    }
    
    public function isVisibleInCatalog()
    {
        return in_array($this->getStatus(), $this->getVisibleInCatalogStatuses());
    }
    
    public function isSalable()
    {
        return $this->getStatus() == self::STATUS_ENABLED;
    }
    
    public function isSaleable()
    {
        return $this->isSalable();
    }
    
    public function isInStock()
    {
        return $this->getStatus() == self::STATUS_ENABLED;
    }
}
