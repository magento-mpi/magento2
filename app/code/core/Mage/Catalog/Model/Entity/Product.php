<?php
/**
 * Product entity resource model
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Entity_Product extends Mage_Eav_Model_Entity_Abstract
{
    protected $_productStoreTable;
    protected $_categoryProductTable;
    
    public function __construct() 
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('catalog_product')
            ->setConnection(
                $resource->getConnection('catalog_read'),
                $resource->getConnection('catalog_write')
            );
            
        $this->_productStoreTable   = $resource->getTableName('catalog/product_store');
        $this->_categoryProductTable= $resource->getTableName('catalog/category_product');
    }
    
    protected function _beforeSave(Varien_Object $object)
    {
        return parent::_beforeSave($object);
    }
    
    protected function _afterSave(Varien_Object $object)
    {
        $this->_saveStores($object)
            ->_saveCategories($object)
            ->_saveLinkedProducts($object);
            
    	return parent::_afterSave($object);
    }
    
    /**
     * Save product stores configuration
     *
     * @param   Varien_Object $object
     * @return  this
     */
    protected function _saveStores(Varien_Object $object)
    {
        $postedStores = $object->getPostedStores();
        
        // If product saving from some store
        if ($object->getStoreId()) {
            if (!is_null($postedStores) && empty($postedStores)) {
                $this->_removeFromStore($object, $object->getStoreId());
                $object->setData('store_id', null);
            }
        }
        // If product saving from default store
        else {
            // Retrieve current stores collection of product
            $storeIds = $this->getStoreIds($object);
            
            if (!isset($postedStores[0])) {
                $postedStores[0] = false;
            }
            
            $postedStoresIds = array_keys($postedStores);

            $insertStoreIds = array_diff($postedStoresIds, $storeIds);
            $deleteStoreIds = array_diff($storeIds, $postedStoresIds);
            
            // Insert in stores
            foreach ($insertStoreIds as $storeId) {
            	$this->_insertToStore($object, $storeId, $postedStores[$storeId]);
            }
            
            // Delete product from stores
            foreach ($deleteStoreIds as $storeId) {
            	$this->_removeFromStore($object, $storeId);
            }
        }
        return $this;
    }
    
    /**
     * Remove product data from some store
     *
     * @param   Mage_Catalog_Model_Product $product
     * @param   int $storeId
     * @return  this
     */
    protected function _removeFromStore($product, $storeId)
    {
        $attributes = $this->getAttributesByTable();
        $tables = array_keys($attributes);
        foreach ($tables as $tableName) {
            $this->getWriteConnection()->delete(
                $tableName,
                $this->getWriteConnection()->quoteInto('store_id=? AND ', $storeId).
                $this->getWriteConnection()->quoteInto($this->getEntityIdField().'=? ', $product->getData($this->getEntityIdField()))
            );        
        }
        
        $this->getWriteConnection()->delete(
            $this->_productStoreTable,
            $this->getWriteConnection()->quoteInto('product_id=? AND ', $product->getId()).
            $this->getWriteConnection()->quoteInto('store_id=?', $storeId)
        );        
        return $this;
    }
    
    /**
     * Insert product from $baseStoreId to $storeId
     *
     * @param Mage_Catalog_Model_Product $product
     * @param int $storeId
     * @param int $baseStoreId
     * @return this
     */
    public function _insertToStore($product, $storeId, $baseStoreId = 0)
    {
    	$data = array(
    	   'store_id'   => (int) $storeId,
    	   'product_id' => $product->getId(),
    	);
    	$this->getWriteConnection()->insert($this->_productStoreTable, $data);
    	
    	if ($storeId && ($storeId != $baseStoreId)) {
    	    $newProduct = Mage::getModel('catalog/product')
    	       ->setStoreId($baseStoreId)
    	       ->load($product->getId());
            if ($newProduct->getId()) {
                $newProduct
                    ->setStoreId($storeId)
                    ->save();
            }
    	}
    	return $this;
    }
    
    protected function _saveCategories(Varien_Object $object)
    {
        $postedCategories = $object->getPostedCategories();
        $oldCategories    = $this->getCategoryCollection($object)
            ->load();
        
        $delete = array();
        $insert = array();
        
        if (!is_array($postedCategories)) {
            $postedCategories = array();
        }
        $categories = array();
        
        foreach ($oldCategories as $category) {
            if ($object->getStoreId()) {
                $stores = $category->getStoreIds();
                if (!in_array($object->getStoreId(), $stores)) {
                    continue;
                }
            }
            
            $categories[] = $category->getId();
        }
        
        $delete = array_diff($categories, $postedCategories);
        $insert = array_diff($postedCategories, $categories);
        
        // Delete unselected category
        if (!empty($delete)) {
            $this->getWriteConnection()->delete(
                $this->_categoryProductTable,
                $this->getWriteConnection()->quoteInto('product_id=? AND ', (int)$object->getId()) .
                $this->getWriteConnection()->quoteInto('category_id in(?)', $delete)
            );                
        }
        
        foreach ($insert as $categoryId) {
            if (empty($categoryId)) {
                continue;
            }
        	$data = array(
        	   'product_id'    => $object->getId(),
        	   'category_id'   => $categoryId,
        	   'position'      => '0'
        	);
        	$this->getWriteConnection()->insert($this->_categoryProductTable, $data);
        }
        return $this;
    }
    
    protected function _saveLinkedProducts(Varien_Object $object)
    {
        foreach($object->getLinkedProductsForSave() as $linkType=>$data) {
	    	$linkedProducts = $object->getLinkedProducts($linkType)->load();
	      	
	       	foreach($data as $linkId=>$linkAttributes) {
	       		if(!$linkedProduct = $linkedProducts->getItemByColumnValue('product_id', $linkId)) {
	       			$linkedProduct = clone $linkedProducts->getObject();
	       			$linkedProduct->setAttributeCollection($linkedProducts->getLinkAttributeCollection());
	       			$linkedProduct->addLinkData($linkedProducts->getLinkTypeId(), $object, $linkId);
	       		}
	       		
	   			foreach ($linkedProducts->getLinkAttributeCollection() as $attribute) {
	   				if(isset($linkAttributes[$attribute->getCode()])) {
	   					$linkedProduct->setData($attribute->getCode(), $linkAttributes[$attribute->getCode()]);
	   				}
	   			}
	   			
	   			$linkedProduct->save();
	       	}
	       	
	       	// Now delete unselected items
	       	
	       	foreach($linkedProducts as $linkedProduct) {
				if(!isset($data[$linkedProduct->getId()])) {
					$linkedProduct->delete();
				}
	       	}
    	}
    	return $this;
    }
    
    public function getCategoryCollection($product)
    {
        $collection = Mage::getResourceModel('catalog/category_collection')
            ->joinField('product_id', 
                'catalog/category_product', 
                'product_id', 
                'category_id=entity_id', 
                null)
            ->addFieldToFilter('product_id', (int) $product->getId());
        return $collection;
    }
    
    
    public function getBundleOptionCollection($product)
    {
    	$collection = Mage::getModel('catalog/product_bundle_option')->getResourceCollection()
    			->setProductIdFilter($product->getId())
    			->setStoreId($product->getStoreId());
    	
    	
    	return $collection;
    }

    public function getStoreCollection($product)
    {
        $collection = Mage::getResourceModel('core/store_collection');
        /* @var $collection Mage_Core_Model_Mysql4_Collection_Abstract */
        
        $collection->getSelect()
            ->join($this->_productStoreTable, $this->_productStoreTable.'.store_id=main_table.store_id')
            ->where($this->_productStoreTable.'.product_id='.(int)$product->getId());

        return $collection;
    }
    
    public function getStoreIds($product)
    {
        $stores = array();
        $collection = $this->getStoreCollection($product)
            ->load();
        foreach ($collection as $store) {
        	$stores[] = $store->getId();
        }
        return $stores;
    }
}
