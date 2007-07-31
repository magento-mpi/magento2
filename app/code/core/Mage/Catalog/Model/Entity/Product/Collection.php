<?php
/**
 * Product collection
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Entity_Product_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    protected $_productStoreTable;
    protected $_storeTable;
    
    public function __construct() 
    {
        $this->setEntity(Mage::getResourceSingleton('catalog/product'));
        $this->setObject('catalog/product');
        
        $this->_productStoreTable = Mage::getSingleton('core/resource')->getTableName('catalog/product_store');
        $this->_storeTable        = Mage::getSingleton('core/resource')->getTableName('core/store');
    }
    
    public function addStoreNamesToResult()
    {
        $productStores = array();
        foreach ($this as $product) {
        	$productStores[$product->getId()] = array();
        }
        
        if (!empty($productStores)) {
            $select = $this->_read->select()
                ->from($this->_productStoreTable)
                ->join($this->_storeTable, $this->_storeTable.'.store_id='.$this->_productStoreTable.'.store_id')
                ->where($this->_read->quoteInto($this->_productStoreTable.'.product_id IN (?)', array_keys($productStores)))
                ->where($this->_storeTable.'.store_id>0');

            $data = $this->_read->fetchAll($select);
            foreach ($data as $row) {
            	$productStores[$row['product_id']][] = $row['name'];
            }
        }
        
        foreach ($this as $product) {
            if (isset($productStores[$product->getId()])) {
                $product->setData('stores', $productStores[$product->getId()]);
            }
        }
        return $this;
    }
}
