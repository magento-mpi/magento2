<?php
/**
 * Product attributes set
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product_Attribute_Set extends Mage_Catalog_Model_Mysql4 implements Mage_Core_Model_Db_Table_Interface 
{
    protected $_setTable;
    protected $_inSetTable;
    
    public function __construct() 
    {
        parent::__construct();
        $this->_setTable    = Mage::registry('resources')->getTableName('catalog', 'product_attribute_set');
        $this->_inSetTable  = Mage::registry('resources')->getTableName('catalog', 'product_attribute_in_set');
    }
    
    /**
     * Get attribute set attributes
     *
     * @param   int $setId
     * @return  array
     */
    public function getAttributes($setId)
    {
        $arrRes =array();
        $sql = "SELECT
                    attribute_id
                FROM
                    $this->_inSetTable
                WHERE
                    product_attribute_set_id=:set_id";
        $arrRes = self::$_read->fetchCol($sql, array('set_id'=>$setId));
        return $arrRes;
    }
    
    /**
     * Get attribute set attributes information
     *
     * @param   int $setId
     * @return  array
     */
    public function getAttributesInfo($setId)
    {
        $attributeTable = Mage::registry('resources')->getTableName('catalog', 'product_attribute');
        $arrRes =array();
        $sql = "SELECT
                    $attributeTable.*
                FROM
                    $this->_inSetTable,
                    $attributeTable
                WHERE
                    $attributeTable.attribute_id=$this->_inSetTable.attribute_id
                    AND product_attribute_set_id=:set_id";

        $arrRes = self::$_read->fetchAll($sql, array('set_id'=>$setId));
        return $arrRes;
    }

    public function getGroups($setId)
    {
        $arrRes =array();
        $groupTable = Mage::registry('resources')->getTableName('catalog', 'product_attribute_group');
        $sql = "SELECT
                    DISTINCT $groupTable.*
                FROM
                    $this->_inSetTable,
                    $groupTable
                WHERE
                    $groupTable.product_attribute_group_id=$this->_inSetTable.product_attribute_group_id
                    AND $this->_inSetTable.product_attribute_set_id=:set_id";
        $arrRes = self::$_read->fetchAll($sql, array('set_id'=>$setId));
        return $arrRes;
    }
    
    /**
     * Insert row in database table
     *
     * @param array $data
     */
    public function insert($data)
    {
        if (self::$_write->insert($this->_setTable, $data)) {
            return self::$_write->lastInsertId();
        }
        return false;
    }
    
    /**
     * Update row in database table
     *
     * @param   array $data
     * @param   int   $rowId
     */
    public function update($data, $rowId)
    {
        $condition = self::$_write->quoteInto('product_attribute_set_id=?', $rowId);
        return self::$_write->update($this->_setTable, $data, $condition);
    }
    
    /**
     * Delete row from database table
     *
     * @param   int $rowId
     */
    public function delete($rowId)
    {
        $condition = self::$_write->quoteInto('product_attribute_set_id=?', $rowId);
        return self::$_write->delete($this->_setTable, $condition);
    }
    
    /**
     * Get row from database table
     *
     * @param   int $rowId
     */
    public function getRow($rowId)
    {
        $sql = "SELECT * FROM $this->_setTable WHERE product_attribute_set_id=:set_id";
        return self::$_read->fetchRow($sql, array('set_id'=>$rowId));
    }    
}