<?php
/**
 * Product attributes groups
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Resource_Model_Mysql4_Product_Attribute_Group extends Mage_Catalog_Resource_Model_Mysql4 implements Mage_Core_Resource_Model_Db_Table_Interface 
{
    protected $_attributeGeoupTable;
    
    public function __construct() 
    {
        parent::__construct();
        $this->_attributeGeoupTable = Mage::registry('resources')->getTableName('catalog', 'product_attribute_group');
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
        $arrRes = $this->_read->fetchRow($sql, array('group_id'=>$groupId));
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
        $attributeTable = Mage::registry('resources')->getTableName('catalog', 'product_attribute');
        
        $attributeInSetTable = Mage::registry('resources')->getTableName('catalog', 'product_attribute_in_set');
        
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
        
        $arrRes = $this->_read->fetchAll($sql, $arrSqlParam);
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
        if ($this->_write->insert($this->_attributeGeoupTable, $data)) {
            return $this->_write->lastInsertId();
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
        $condition = $this->_write->quoteInto('product_attribute_group_id=?', $rowId);
        return $this->_write->update($this->_attributeGeoupTable, $data, $condition);
    }
    
    /**
     * Delete row from database table
     *
     * @param   int $rowId
     */
    public function delete($rowId)
    {
        $condition = $this->_write->quoteInto('product_attribute_group_id=?', $rowId);
        return $this->_write->delete($this->_attributeGeoupTable, $condition);
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