<?php
/**
 * Product attributes groups
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product_Attribute_Group
{
    protected $_attributeGeoupTable;
    static protected $_read;
    static protected $_write;

    public function __construct() 
    {
        self::$_read = Mage::registry('resources')->getConnection('catalog_read');
        self::$_write = Mage::registry('resources')->getConnection('catalog_write');
        $this->_attributeGeoupTable = Mage::registry('resources')->getTableName('catalog_resource', 'product_attribute_group');
    }
    
    /**
     * Get group data
     *
     * @param   int $groupId
     * @param   string || array $fields
     * @return  array
     */
    public function load($groupId)
    {
        $sql = "SELECT * FROM $this->_attributeGeoupTable WHERE group_id=:group_id";
        $arr = self::$_read->fetchRow($sql, array('group_id'=>$groupId));
        return $arr;
    }

    /**
     * Get group attributes
     *
     * @param   int $groupId
     * @param   int $setId
     * @return  array
     */
    public function getAttributes($groupId, $setId)
    {
        $collection = Mage::getModel('catalog_resource', 'product_attribute_collection')
            ->addGroupFilter($groupId)
            ->addSetFilter($setId);
        return $collection;
    }
    
    public function save(Mage_Catalog_Model_Product_Attribute_Group $group)
    {
        
    }
}