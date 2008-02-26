<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Configurable product type implementation
 *
 * This type builds in product attributes and existing simple products
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Product_Type_Configurable extends Mage_Catalog_Model_Product_Type_Abstract
{
    /**
     * Attributes which used for configurable product
     *
     * @var array
     */
    protected $_usedProductAttributeIds = null;
    protected $_usedProductAttributes   = null;
    protected $_configurableAttributes  = null;
    protected $_usedProductIds  = null;
    protected $_usedProducts    = null;

    /**
     * Retrieve product type attributes
     *
     * @return array
     */
    public function getEditableAttributes()
    {
        if (is_null($this->_editableAttributes)) {
            $this->_editableAttributes = parent::getEditableAttributes();
            foreach ($this->_editableAttributes as $index => $attribute) {
                if (!$attribute->getUseInSuperProduct()) {
                    unset($this->_editableAttributes[$index]);
                }
                if ($this->getUsedProductAttributeIds()
                    && in_array($attribute->getAttributeId(), $this->getUsedProductAttributeIds())) {
                    unset($this->_editableAttributes[$index]);
                }
            }
        }
        return $this->_editableAttributes;
    }

    /**
     * Checkin attribute availability for superproduct
     *
     * @param   Mage_Eav_Model_Entity_Attribute $attribute
     * @return  bool
     */
    public function canUseAttribute(Mage_Eav_Model_Entity_Attribute $attribute)
    {
        $allow = $attribute->getIsGlobal()
            && $attribute->getIsVisible()
            && $attribute->getUseInSuperProduct()
            && $attribute->getIsUserDefined()
            && ($attribute->usesSource() || $attribute->getBackendType()=='int' );

//        echo "<Hr>getName:".$attribute->getName();
//        echo ", getIsGlobal:".$attribute->getIsGlobal();
//        echo ", getIsVisible:".$attribute->getIsVisible();
//        echo ", getUseInSuperProduct:".$attribute->getUseInSuperProduct();
//        echo ", getIsUserDefined:".$attribute->getIsUserDefined();
//        echo ", usesSource:".$attribute->usesSource();
//        echo ", getBackendType:".$attribute->getBackendType();
//        echo ": ".$allow."<hr>";

        return $allow;
    }

    /**
     * Declare attribute identifiers used for asign subproducts
     *
     * @param   array $ids
     * @return  Mage_Catalog_Model_Product_Type_Configurable
     */
    public function setUsedProductAttributeIds($ids)
    {
        $this->_usedProductAttributes = array();
        $this->_configurableAttributes= array();

        foreach ($ids as $attributeId) {
            $this->_usedProductAttributes[] = $this->getAttributeById($attributeId);
            $this->_configurableAttributes[]= Mage::getModel('catalog/product_type_configurable_attribute')
                ->setProductAttribute($this->getAttributeById($attributeId));
        }
        $this->_usedProductAttributeIds = $ids;
        return $this;
    }

    /**
     * Retrieve identifiers of used product attributes
     *
     * @return array
     */
    public function getUsedProductAttributeIds()
    {
        if (is_null($this->_usedProductAttributeIds)) {
            $this->_usedProductAttributeIds = array();
            foreach ($this->getUsedProductAttributes() as $attribute) {
            	$this->_usedProductAttributeIds[] = $attribute->getId();
            }
        }
        return $this->_usedProductAttributeIds;
    }

    /**
     * Retrieve used product attributes
     *
     * @return array
     */
    public function getUsedProductAttributes()
    {
        if (is_null($this->_usedProductAttributes)) {
            $this->_usedProductAttributes = array();
            foreach ($this->getConfigurableAttributes() as $attribute) {
            	$this->_usedProductAttributes[] = $attribute->getProductAttribute();
            }
        }
        return $this->_usedProductAttributes;
    }

    /**
     * Retrieve configurable attrbutes data
     *
     * @return array
     */
    public function getConfigurableAttributes()
    {
        if (is_null($this->_configurableAttributes)) {
            $this->_configurableAttributes = array();
            foreach ($this->getConfigurableAttributeCollection() as $attribute) {
                $attribute->setProductAttribute($this->getAttributeById($attribute->getAttributeId()));
            	$this->_configurableAttributes[] = $attribute;
            }
        }
        return $this->_configurableAttributes;
    }

    public function getConfigurableAttributesAsArray()
    {
        $res = array();
        foreach ($this->getConfigurableAttributes() as $attribute) {
        	$res[] = array(
        	   'id'            => $attribute->getId(),
        	   'label'         => $attribute->getLabel(),
        	   'position'      => $attribute->getPosition(),
        	   'values'        => $attribute->getPrices() ? $attribute->getPrices() : array(),
        	   'attribute_id'  => $attribute->getProductAttribute()->getId(),
        	   'attribute_code'=> $attribute->getProductAttribute()->getAttributeCode(),
        	   'frontend_label'=> $attribute->getProductAttribute()->getFrontendLabel(),
        	);
        }
        return $res;
    }

    public function getConfigurableAttributeCollection()
    {
        return Mage::getResourceModel('catalog/product_type_configurable_attribute_collection')
            ->setProductFilter($this->getProduct());
    }


    /**
     * Retrieve subproducts identifiers
     *
     * @return array
     */
    public function getUsedProductIds()
    {
        if (is_null($this->_usedProductIds)) {
            $this->_usedProductIds = array();
            foreach ($this->getUsedProducts() as $product) {
            	$this->_usedProductIds[] = $product->getId();
            }
        }
        return $this->_usedProductIds;
    }

    /**
     * Retrieve array of "subproducts"
     *
     * @return array
     */
    public function getUsedProducts()
    {
        if (is_null($this->_usedProducts)) {
            $this->_usedProducts = array();
            foreach ($this->getUsedProductCollection() as $product) {
                $configurableSetings = array();
                foreach ($this->getUsedProductAttributes() as $attribute) {
                    $configurableSetings[] = array(
                        'attribute_id'  => $attribute->getId(),
                        'value_index'   => $product->getData($attribute->getAttributeCode()),
                        'label'         => $attribute->getFrontend()->getLabel()
                    );
                }
                $product->setConfigurableSettings($configurableSetings);
            	$this->_usedProducts[] = $product;
            }
        }
        return $this->_usedProducts;
    }

    /**
     * Retrieve related products collection
     *
     * @return unknown
     */
    public function getUsedProductCollection()
    {
        $collection = Mage::getResourceModel('catalog/product_type_configurable_product_collection')
            ->setProductFilter($this->getProduct());
        foreach ($this->getUsedProductAttributes() as $attribute) {
        	$collection->addAttributeToSelect($attribute->getId());
        }
        return $collection;
    }

    /**
     * Save configurable product depended data
     *
     * @return Mage_Catalog_Model_Product_Type_Configurable
     */
    public function save()
    {
        parent::save();
        /**
         * Save Attributes Information
         */
        if ($data = $this->getProduct()->getConfigurableAttributesData()) {
            foreach ($data as $attributeData) {
            	$attribute = Mage::getModel('catalog/product_type_configurable_attribute')
            	   ->setData($attributeData)
            	   ->setStoreId($this->getProduct()->getStoreId())
            	   ->setProductId($this->getProduct()->getId())
            	   ->save();
            }
        }

        /**
         * Save product relations
         */
        if ($data = $this->getProduct()->getConfigurableProductsData()) {
            $productIds = array_keys($data);
            Mage::getResourceModel('catalog/product_type_configurable')
                ->saveProducts($this->getProduct()->getId(), $productIds);
        }
        return $this;
    }


/*
   	public function getSuperAttributes($product, $asObject=false, $applyLinkFilter=false)
   	{
   		$result = array();
   		if(!$product->getId()) {
    		$position = 0;
    		$superAttributesIds = $product->getSuperAttributesIds();
    		foreach ($product->getAttributes() as $attribute) {
    			if(in_array($attribute->getAttributeId(), $superAttributesIds)) {
    				if(!$asObject) {
						$row = $attribute->toArray(array('attribute_id','attribute_code','id','frontend_label'));
						$row['values'] = array();
						$row['label'] = $row['frontend_label'];
						$row['position'] = $position++;
    				} else {
    					$row = $attribute;
    				}
    				$result[] = $row;
    			}
    		}
    	} else {
    		if($applyLinkFilter) {
    			if(!$product->getSuperLinkCollection()->getIsLoaded()) {
	    			$product->getSuperLinkCollection()
	    				->joinField('store_id',
					                'catalog/product_store',
					                'store_id',
					                'product_id=entity_id',
					                '{{table}}.store_id='.(int) $product->getStoreId());
	    			$product->getSuperAttributeCollection()->getPricingCollection()
	    					->addLinksFilter($product->getSuperLinks());
                    $product->getSuperAttributeCollection()->getPricingCollection()->clear();
	    			$product->getSuperAttributeCollection()->clear();
	    			$product->getSuperAttributeCollection()->load();

    			}
    		}

    		$superAttributesIds = $product->getSuperAttributeCollectionLoaded()->getColumnValues('attribute_id');
    		foreach ($superAttributesIds as $attributeId) {
                foreach($product->getAttributes() as $attribute) {
    		    	if ($attributeId == $attribute->getAttributeId()) {
        				if(!$asObject) {
        					$superAttribute = $product->getSuperAttributeCollectionLoaded()->getItemByColumnValue('attribute_id', $attribute->getAttributeId());
    						$row = $attribute->toArray(array('attribute_id','attribute_code','frontend_label'));
    						$row['values'] = $superAttribute->getValues($attribute);
    						$row['label'] = $superAttribute->getLabel();
    						$row['id'] = $superAttribute->getId();
    						$row['position'] = $superAttribute->getPosition();
        				} else {
        					$row = $attribute;
        				}
        				$result[] = $row;
    		    	}
    		    }
    		}
    	}
    	return $result;
   	}
    */
}
