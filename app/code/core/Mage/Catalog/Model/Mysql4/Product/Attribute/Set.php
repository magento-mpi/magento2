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
        $this->_setTable    = $this->getTableName('catalog_read', 'product_attribute_set');
        $this->_inSetTable  = $this->getTableName('catalog_read', 'product_attribute_in_set');
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
        $arrRes = $this->_read->fetchCol($sql, array('set_id'=>$setId));
        return $arrRes;
    }
    
    public function getGroups($setId)
    {
        $arrRes =array();
        $groupTable = $this->getTableName('catalog_read', 'product_attribute_group');
        $sql = "SELECT
                    DISTINCT $groupTable.*
                FROM
                    $this->_inSetTable,
                    $groupTable
                WHERE
                    $groupTable.product_attribute_group_id=$this->_inSetTable.product_attribute_group_id
                    AND $this->_inSetTable.product_attribute_set_id=:set_id";
        $arrRes = $this->_read->fetchAll($sql, array('set_id'=>$setId));
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
        
    }    
}