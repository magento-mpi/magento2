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
    public function get($groupId, $fields = '*')
    {
        if (is_array($fields)) {
            $fields = implode(', ', $fields);
        }
        
        $sql = "SELECT $fields FROM $this->_attributeGeoupTable WHERE product_attribute_group_id=:group_id";
        $arrRes = self::$_read->fetchRow($sql, array('group_id'=>$groupId));
        return $arrRes;
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
        $arrRes = array();
        $attributeTable = Mage::registry('resources')->getTableName('catalog_resource', 'product_attribute');
        
        $attributeInSetTable = Mage::registry('resources')->getTableName('catalog_resource', 'product_attribute_in_set');
        
        $sql = "SELECT
                    $attributeTable.*
                FROM
                    $attributeTable,
                    $attributeInSetTable
                WHERE
                    $attributeTable.attribute_id=$attributeInSetTable.attribute_id
                    AND $attributeInSetTable.product_attribute_group_id=:group_id
                    AND $attributeInSetTable.product_attribute_set_id=:set_id
                ORDER BY
                    $attributeInSetTable.position";
        
        $arrSqlParam = array();
        $arrSqlParam['set_id']  = $setId;
        $arrSqlParam['group_id']= $groupId;
        
        $arrRes = self::$_read->fetchAll($sql, $arrSqlParam);
        return $arrRes;
    }
    
    /**
     * Insert row in database table
     *
     * @param   array $data
     * @return  int || false
     */
    public function insert($data)
    {
        if (self::$_write->insert($this->_attributeGeoupTable, $data)) {
            return self::$_write->lastInsertId();
        }
        return false;
    }
    
    /**
     * Update row in database table
     *
     * @param   array $data
     * @param   int   $rowId
     * @return  int
     */
    public function update($data, $rowId)
    {
        $condition = self::$_write->quoteInto('product_attribute_group_id=?', $rowId);
        return self::$_write->update($this->_attributeGeoupTable, $data, $condition);
    }
    
    /**
     * Delete row from database table
     *
     * @param   int $rowId
     */
    public function delete($rowId)
    {
        $condition = self::$_write->quoteInto('product_attribute_group_id=?', $rowId);
        return self::$_write->delete($this->_attributeGeoupTable, $condition);
    }
    
    /**
     * Get row from database table
     *
     * @param   int $rowId
     */
    public function getRow($rowId)
    {
        return $this->get($rowId);
    }    
}