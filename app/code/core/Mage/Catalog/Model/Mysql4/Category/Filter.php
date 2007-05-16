<?php
/**
 * Category filter Mysql4 resource
 *
 * @package    Mage
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Category_Filter
{
    protected $_filterTable;
    protected $_filterValueTable;
    protected $_write;
    protected $_read;
    
    public function __construct() 
    {
        $this->_filterTable = Mage::registry('resources')->getTableName('catalog_resource', 'category_filter');
        $this->_filterValueTable = Mage::registry('resources')->getTableName('catalog_resource', 'category_filter_value');
        $this->_write = Mage::registry('resources')->getConnection('catalog_write');
        $this->_read  = Mage::registry('resources')->getConnection('catalog_read');
    }
    
    public function load($filterId)
    {
        
    }
    
    public function getValues($filterId)
    {
        $sql = "SELECT * FROM $this->_filterValueTable WHERE filter_id=:id ORDER BY position";
        $arr = $this->_read->fetchAll($sql, array('id'=>$filterId));
        return $arr;
    }
}