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
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Product_Type_Configurable extends Mage_Catalog_Model_Product_Type_Abstract
{
    /**
     * Retrieve product type attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        if (!$this->_attributes) {
            $this->_attributes = parent::getAttributes();
            foreach ($this->_attributes as $index => $attribute) {
                if (!$attribute->getUseInSuperProduct()) {
                    unset($this->_attributes[$index]);
                }
                if ($this->getProduct()->getSuperAttributesIds()
                    && in_array($attribute->getAttributeId(), $this->getProduct()->getSuperAttributesIds())) {
                    unset($this->_attributes[$index]);
                }
            }
        }
        return $this->_attributes;
    }


/*    public function _saveSuperConfig($object)
    {
    	if(!$object->isSuperConfig()) {
    		return $this;
    	}

    	$attributes = $object->getSuperAttributesForSave();
    	if ($attributes) {
        	foreach($attributes as $attribute) {
        		$attributeModel = Mage::getModel('catalog/product_super_attribute')
        			->setData($attribute)
        			->setStoreId($object->getStoreId())
        			->setProductId($object->getId())
        			->setId($attribute['id'])
        			->save();
        	}
    	}

    	$linkExistsProductIds = array();
    	$links = $object->getSuperLinksForSave();
    	foreach (array_keys($links) as $productId) {
    		$linkModel = Mage::getModel('catalog/product_super_link')
    			->loadByProduct($productId, $object->getId())
    			->setProductId($productId)
    			->setParentId($object->getId())
    			->save();

    		$linkExistsProductIds[] = $productId;
    	}

    	$linkCollection = $this->getSuperLinkCollection($object)->load();

    	foreach($linkCollection as $item) {
    		if(!in_array($item->getProductId(), $linkExistsProductIds)) {
    			$item->delete();
    		}
    	}

    	return $this;
    }


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

   	public function getSuperLinks($product)
   	{
   		$result = array();

   		$attributes = $product->getSuperAttributes(true);
   		if(!$product->getSuperLinkCollection()->getIsLoaded()) {
	   		$product->getSuperLinkCollection()
	   				->useProductItem();

	   		foreach ($attributes as $attribute) {
	   			$product->getSuperLinkCollection()
	   				->addAttributeToSelect($attribute->getAttributeCode());
	   		}
   		}

   		foreach ($product->getSuperLinkCollectionLoaded() as $link) {
   			$resultAttributes = array();
   			foreach($attributes as $attribute) {
   				$resultAttribute = array();
   				$resultAttribute['attribute_id'] = $attribute->getAttributeId();
   				$resultAttribute['value_index']	 = $link->getData($attribute->getAttributeCode());
   				$resultAttribute['label']	     = $attribute->getFrontend()->getLabel();
   				$resultAttributes[] 			 = $resultAttribute;
   			}

   			$result[$link->getEntityId()] = $resultAttributes;
   		}

    	return $result;
   	}

    public function getSuperAttributeCollection($product)
    {
    	$collection = Mage::getResourceModel('catalog/product_super_attribute_collection');
    	$collection->setProductFilter($product)
    		->setOrder('position', 'asc');
    	return $collection;
    }

    public function getSuperLinkCollection($product)
    {
    	$collection = Mage::getResourceModel('catalog/product_super_link_collection');
    	$collection->setProductFilter($product);
    	return $collection;
    }



    */


}
