<?php
/**
 * Product attributes set collection
 *
 * @package    Mage
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product_Attribute_Option_Collection extends Varien_Data_Collection_Db
{
    protected $_optionTable;
    protected $_storeId;
    
    public function __construct() 
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('catalog_read'));
        $this->_optionTable    = Mage::getSingleton('core/resource')->getTableName('catalog/product_attribute_option');
        
        $this->_sqlSelect->from($this->_optionTable);
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('catalog/product_attribute_option'));
    }
    
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
    }
    
    public function getStoreId()
    {
        if ($this->_storeId) {
            return $this->_storeId;
        }
        return Mage::getSingleton('core/store')->getId();
    }
    
    public function loadData($printQuery = false, $logQuery = false)
    {
        $this->addFilter('store_id', $this->getStoreId());
        parent::loadData($printQuery, $logQuery);
        return $this;
    }
    
    public function addAttributeFilter($attributeId)
    {
        $this->addFilter('attribute_id', $attributeId);
        return $this;
    }
    
    public function getArrItemId()
    {
        $arr = array();
        foreach ($this as $option) {
            $arr[] = $option->getId();
        }
        return $arr;
    }
}
