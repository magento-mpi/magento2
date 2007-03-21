<?php
/**
 * Product attributes groups
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product_Attribute_Group extends Mage_Catalog_Model_Mysql4 implements Mage_Core_Model_Db_Table_Interface 
{
    protected $_attributeGeoupTable;
    protected $_attributeInGroupTable;
    
    public function __construct() 
    {
        parent::__construct();
        $this->_attributeGeoupTable = $this->getTableName('catalog_read', 'product_attribute_group');
        $this->_attributeInGeoupTable = $this->getTableName('catalog_read', 'product_attribute_in_group');
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
    public function getAttributes($groupId, $setId = false)
    {
        $arrRes = array();
        $attributeTable = $this->getTableName('catalog_read', 'product_attribute');
        $arrSqlParam = array('group_id'=>$groupId);
        
        if ($setId) {
            $attributeInSetTable = $this->getTableName('catalog_read', 'product_attribute_in_set');
            
            $sql = "SELECT
                        $attributeTable.*
                    FROM
                        $this->_attributeInGeoupTable,
                        $attributeTable,
                        $attributeInSetTable
                    WHERE
                        $attributeTable.attribute_id=$this->_attributeInGeoupTable.attribute_id
                        AND $attributeInSetTable.attribute_id=$attributeTable.attribute_id
                        AND $this->_attributeInGeoupTable.product_attribute_group_id=:group_id
                        AND $attributeInSetTable.product_attribute_set_id=:set_id
                    ORDER BY
                        $this->_attributeInGeoupTable.position";
            $arrSqlParam['set_id'] = $setId;
        }
        else {
            $sql = "SELECT
                        $attributeTable.*
                    FROM
                        $this->_attributeInGeoupTable,
                        $attributeTable
                    WHERE
                        $this->_attributeInGeoupTable.product_attribute_group_id=:group_id
                        AND $attributeTable.attribute_id=$this->_attributeInGeoupTable.attribute_id
                    ORDER BY
                        $this->_attributeInGeoupTable.position";
        }
        $arrRes = $this->_read->fetchAll($sql, $arrSqlParam);
        return $arrRes;
    }
    
    /**
     * Insert row in database table
     *
     * @param array $data
     */
    public function insert($data)
    {
        
    }
    
    /**
     * Update row in database table
     *
     * @param   array $data
     * @param   int   $rowId
     */
    public function update($data, $rowId)
    {
        
    }
    
    /**
     * Delete row from database table
     *
     * @param   int $rowId
     */
    public function delete($rowId)
    {
        
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