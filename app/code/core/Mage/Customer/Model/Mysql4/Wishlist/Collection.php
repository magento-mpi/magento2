<?php
/**
 * Wishlist mysql4 collection model
 *
 * @package    Mage
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Model_Mysql4_Wishlist_Collection extends Varien_Data_Collection_Db
{
    protected $_wishlistTable;
    protected $_productCollection;
    
    public function __construct() 
    {
        parent::__construct(Mage::registry('resources')->getConnection('catalog_read'));
        
        $this->_wishlistTable = Mage::registry('resources')->getTableName('customer_resource', 'wishlist');
        $this->_productCollection = Mage::getModel('catalog_resource', 'product_collection');
        $this->_sqlSelect->from($this->_wishlistTable);
    }
    
    public function getProductCollection()
    {
        return $this->_productCollection;
    }

    public function addCustomerFilter($condition)
    {
        $this->_sqlSelect->where($this->_getConditionSql("$this->_wishlistTable.customer_id", $condition));
        return $this;
    }   
    
    protected function _loadLinkedProducts()
    {
        $arrProductId = $this->getColumnValues('product_id');
        if (empty($arrProductId)) {
            return false;
        }
        $this->getProductCollection()->addProductFilter(array('in'=>$arrProductId));
        $linkedProducts = $this->getProductCollection()->loadData();
            
        foreach ($this->getItems() as $item) {
            $item->setProduct($linkedProducts->getItemById($item->getProductId()));
        }
        return true;
    }
    
    public function loadData($printQuery=false, $logQuery=false)
    {
        if (!parent::loadData($printQuery, $logQuery)) {
            return $this;
        }
        
        $this->_loadLinkedProducts();        
        return $this;
    }
}