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
 * Product entity resource model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Resource_Eav_Mysql4_Product extends Mage_Catalog_Model_Resource_Eav_Mysql4_Abstract
{
    protected $_productWebsiteTable;
    protected $_productCategoryTable;

    /**
     * Initialize resource
     */
    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('catalog_product')
            ->setConnection(
                $resource->getConnection('catalog_read'),
                $resource->getConnection('catalog_write')
            );

        $this->_productWebsiteTable = $resource->getTableName('catalog/product_website');
        $this->_productCategoryTable= $resource->getTableName('catalog/category_product');
    }

    /**
     * Default product attributes
     *
     * @return array
     */
    protected function _getDefaultAttributes()
    {
        return array('entity_type_id', 'attribute_set_id', 'type_id', 'created_at', 'updated_at');
    }

    /**
     * Retrieve product website identifiers
     *
     * @param   $product
     * @return  Mage_Catalog_Model_Resource_Eav_Mysql4_Product
     */
    public function getWebsiteIds($product)
    {
        $select = $this->_getWriteAdapter()->select()
            ->from($this->_productWebsiteTable, 'website_id')
            ->where('product_id=?', $product->getId());
        return $this->_getWriteAdapter()->fetchCol($select);
    }

    public function getIdBySku($sku)
    {
         return $this->_read->fetchOne('select entity_id from '.$this->getEntityTable().' where sku=?', $sku);
    }

    protected function _beforeSave(Varien_Object $object)
    {
        if (!$object->getId() && $object->getSku()) {
           $object->setId($this->getIdBySku($object->getSku()));
        }

        return parent::_beforeSave($object);
    }

    protected function _afterSave(Varien_Object $product)
    {
        parent::_afterSave($product);

        $this->_saveWebsiteIds($product)
            //->_saveCategories($object)
            //->_saveLinkedProducts($object)
            ;

    	return $this;
    }

    protected function _saveWebsiteIds($product)
    {
        $ids = $product->getWebsiteIds();

        $this->_getWriteAdapter()->delete(
            $this->_productWebsiteTable,
            $this->_getWriteAdapter()->quoteInto('product_id=?', $product->getId())
        );

        foreach ($ids as $websiteId) {
            $this->_getWriteAdapter()->insert(
                $this->_productWebsiteTable,
                array('product_id'=>$product->getId(), 'website_id'=>$websiteId)
            );
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
            if ($object->getId()) {
                //no changes made
                return $this;
            } else {
                $postedCategories = array();
            }
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
                $this->_productCategoryTable,
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
        	$this->getWriteConnection()->insert($this->_productCategoryTable, $data);
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

    public function getStoreCollection($product)
    {
        $collection = Mage::getResourceModel('core/store_collection')
            ->setLoadDefault(true);
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

    public function getDefaultAttributeSourceModel()
    {
        return 'eav/entity_attribute_source_table';
    }

    /**
     * Validate all object's attributes against configuration
     *
     * @param Varien_Object $object
     * @return Varien_Object
     */
    public function validate($object)
    {
        parent::validate($object);
        return $this;
    }

    public function copy(Mage_Catalog_Model_Product $object)
    {
        $uniqAttributes = array();


        $storeIds = $this->getStoreIds($object);
        $oldId = $object->getId();

        $storeIds = array_combine($storeIds, array_fill(0, sizeof($storeIds), 0));
        if(!isset($storeIds[0])) {
            $storeIds[0] = 0;
        }

        $catagoryCollection = $this->getCategoryCollection($object)
            ->load();
        $categories = array();
        foreach ($catagoryCollection as $category) {
        	$categories[] = $category->getId();
        }

        $object->setStoreId(0)
            ->load($object->getId());

        $newProduct = Mage::getModel('catalog/product')
	       ->setStoreId(0)
	       ->addData($object->getData());

        $this->_prepareCopy($newProduct);
        Mage::dispatchEvent('catalog_product_copy', array('product'=>$object, 'new_product'=>$newProduct));
        $newProduct->setPostedStores($storeIds);
        $newProduct->setPostedCategories($categories);
        $newProduct->save();

        $newId = $newProduct->getId();

        foreach ($storeIds as $storeId) {
        	if ($storeId) {
        	    $oldProduct = Mage::getModel('catalog/product')
        	       ->setStoreId($storeId)
        	       ->load($oldId);

                $newProduct = Mage::getModel('catalog/product')
        	       ->setStoreId($storeId)
        	       ->load($newId)
        	       ->addData($oldProduct->getData());

                $this->_prepareCopy($newProduct);
                $newProduct->setId($newId);
                $newProduct->save();
        	}
        }
        $object->setId($newId);
        return $this;
    }

    protected function _prepareCopy($object)
    {
        $object->setId(null);
        foreach ($object->getAttributes() as $attribute) {
        	if ($attribute->getIsUnique()) {
        	    $object->setData($attribute->getAttributeCode(), null);
        	}
        }
        $object->setStatus(Mage_Catalog_Model_Product_Status::STATUS_DISABLED);
        return $this;
    }
}
