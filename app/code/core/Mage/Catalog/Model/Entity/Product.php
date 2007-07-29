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
        return $this;
    }
    
    protected function _afterSave(Varien_Object $object)
    {
        $this->_saveStores($object)
            ->_saveCategories($object)
            ->_saveLinkedProducts($object);
            
    	return parent::_afterSave($object);
    }
    
    protected function _saveStores(Varien_Object $object)
    {
        $postedStores = $object->getPostedStores();
        if ($object->getStoreId()) {
            if (empty($postedStores)) {
                $this->getWriteConnection()->delete(
                    $this->_productStoreTable,
                    $this->getWriteConnection()->quoteInto('product_id=? AND', $object->getId()).
                    $this->getWriteConnection()->quoteInto('store_id=?', $object->getStoreId())
                );
            }
        }
        else {
            $this->getWriteConnection()->delete(
                $this->_productStoreTable,
                $this->getWriteConnection()->quoteInto('product_id=?', $object->getId())
            );
            if (!in_array(0, $postedStores)) {
                $postedStores[] = 0;
            }
            foreach ($postedStores as $storeId) {
            	$data = array(
            	   'product_id' => $object->getId(),
            	   'store_id'   => (int) $storeId
            	);
            	$this->getWriteConnection()->insert($this->_productStoreTable, $data);
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
        $insert = is_array($postedCategories) ? $postedCategories : array($postedCategories);
        
        foreach ($oldCategories as $category) {
            if ($object->getStoreId()) {
                $stores = $category->getStoreIds();
                if (!in_array($object->getStoreId(), $stores)) {
                    continue;
                }
            }
            
            $key = array_search($category->getId(), $insert);
        	if ($key !== false) {
        	    $delete[] = $category->getId();
        	    unset($insert[$key]);
        	}
        }
        
        // Delete unselected category
        if (!empty($delete)) {
            $this->getWriteConnection()->delete(
                $this->_categoryProductTable,
                $this->getWriteConnection()->quoteInto('product_id=? AND ', (int)$object->getId()) .
                $this->getWriteConnection()->quoteInto('category_id in(?)', $delete)
            );                
        }
        foreach ($insert as $categoryId) {
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
	      	
	       	foreach($data['linkIds'] as $index=>$linkId) {
	       		if(!$linkedProduct = $linkedProducts->getItemByColumnValue('product_id', $linkId)) {
	       			$linkedProduct = clone $linkedProducts->getObject();
	       			$linkedProduct->setAttributeCollection($linkedProducts->getLinkAttributeCollection());
	       			$linkedProduct->addLinkData($linkedProducts->getLinkTypeId(), $object, $linkId);
	       		}
	       		
	   			foreach ($linkedProducts->getLinkAttributeCollection() as $attribute) {
	   				if(isset($data['linkAttributes'][$index][$attribute->getCode()])) {
	   					$linkedProduct->setData($attribute->getCode(), $data['linkAttributes'][$index][$attribute->getCode()]);
	   				}
	   			}
	   					
	   			$linkedProduct->save();
	       	}
	       	
	       	// Now delete unselected items
	       	
	       	foreach($linkedProducts as $linkedProduct) {
				if(!in_array($linkedProduct->getId(), $data['linkIds'])) {
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

    public function getStoreCollection($product)
    {
        $collection = Mage::getResourceModel('core/store_collection');
        /* @var $collection Mage_Core_Model_Mysql4_Collection_Abstract */
        
        $collection->getSelect()
            ->join($this->_productStoreTable, $this->_productStoreTable.'.store_id=main_table.store_id')
            ->where($this->_productStoreTable.'.product_id='.(int)$product->getId());

        $collection->load(true);
        return $collection;
    }
}
