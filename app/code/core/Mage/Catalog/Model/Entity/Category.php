<?php
/**
 * Catalog category model
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Entity_Category extends Mage_Eav_Model_Entity_Abstract
{
    protected $_tree;
    protected $_categoryProductTable;
    public function __construct() 
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('catalog_category')
            ->setConnection(
                $resource->getConnection('catalog_read'),
                $resource->getConnection('catalog_write')
            );
        $this->_categoryProductTable = Mage::getSingleton('core/resource')->getTableName('catalog/category_product');
    }
    
    /**
     * Retrieve category tree object
     *
     * @return Varien_Data_Tree_Db
     */
    protected function _getTree()
    {
        return Mage::getResourceModel('catalog/category_tree')->getTree();
    }
    
    protected function _afterDelete(Varien_Object $object){
        parent::_afterDelete($object);
        $node = $this->_getTree()->loadNode($object->getId());
        $this->_getTree()->removeNode($node);
        return $this;
    }
    
    protected function _beforeSave(Varien_Object $object)
    {
        parent::_beforeSave($object);
        $parentNode = $this->_getTree()->loadNode($object->getParentId());
        if ($object->getId()) {
            
        }
        else {
            $node = $this->_getTree()->appendChild(array(), $parentNode);
            $object->setId($node->getId());
        }
        return $this;
    }
    
    protected function _afterSave(Varien_Object $object)
    {
        parent::_afterSave($object);
        $products = $object->getPostedProducts();
        if (!is_null($products)) {
            $oldProducts = $object->getProductIds();
            if (!empty($oldProducts)) {
                $this->getWriteConnection()->delete($this->_categoryProductTable, 
                    $this->getWriteConnection()->quoteInto('product_id in(?)', $oldProducts) . ' AND ' .
                    $this->getWriteConnection()->quoteInto('category_id=?', $object->getId())
                );
            }
            
            foreach ($products as $productId => $productPosition) {
                if (!intval($productId)) {
                    continue;
                }
            	$data = array(
            	   'category_id'   => $object->getId(),
            	   'product_id'    => $productId,
            	   'position'      => $productPosition
            	);
            	$this->getWriteConnection()->insert($this->_categoryProductTable, $data);
            }
        }
        return $this;
    }
    
    protected function _insertAttribute($object, $attribute, $value, $storeIds = array())
    {
        return parent::_insertAttribute($object, $attribute, $value, $this->_getStoresForInsertAttribute($object));
    }
    
    /**
     * Retrieve stores for category attributes insert
     *
     * @param   Varien_Object $object
     * @return  array
     */
    protected function _getStoresForInsertAttribute($object)
    {
        static $stores;
        if (is_null($stores)) {
            $nodePath = $this->_getTree()
                ->load()
                ->getNodeById($object->getId())
                    ->getPath();
            $nodes = array();
            foreach ($nodePath as $node) {
            	$nodes[] = $node->getId();
            }
            $stores = array_keys(Mage::getConfig()->getStoresByPath('catalog/category/root_id', $nodes));
            array_unshift($stores, 0);
        }
        return $stores;
    }
    
    /**
     * Retrieve category product id's
     *
     * @param   Mage_Catalog_Model_Category $category
     * @return  array
     */
    public function getProductIds($category)
    {
        $collection = Mage::getResourceModel('catalog/product_collection')
            ->joinField('category_id', 
                'catalog/category_product', 
                'category_id', 
                'product_id=entity_id', 
                null, 
                'left')
            ->addFieldToFilter('category_id', $category->getId())
            ->load();
        $products = array();
        foreach ($collection as $product) {
        	$products[] = $product->getId();
        }
        return $products;
    }
}
