<?php
/**
 * Product attributes set collection
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product_Attribute_Option_Collection extends Varien_Data_Collection_Db
{
    protected $_optionTable;
    protected $_websiteId;
    
    public function __construct() 
    {
        parent::__construct(Mage::registry('resources')->getConnection('catalog_read'));
        $this->_optionTable    = Mage::registry('resources')->getTableName('catalog_resource', 'product_attribute_option');
        
        $this->_sqlSelect->from($this->_optionTable);
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('catalog', 'product_attribute_option'));
    }
    
    public function setWebsiteId($websiteId)
    {
        $this->_websiteId = $websiteId;
    }
    
    public function getWebsiteId()
    {
        if ($this->_websiteId) {
            return $this->_websiteId;
        }
        return Mage::registry('website')->getId();
    }
    
    public function loadData($printQuery = false, $logQuery = false)
    {
        $this->addFilter('website_id', $this->getWebsiteId());
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
