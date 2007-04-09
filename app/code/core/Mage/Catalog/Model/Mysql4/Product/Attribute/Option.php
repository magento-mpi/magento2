<?php
/**
 * Product attributes options model
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product_Attribute_Option extends Mage_Catalog_Model_Mysql4 implements Mage_Core_Model_Db_Table_Interface 
{
    protected $_optionTable;
    protected $_optionTypeTable;
    
    public function __construct() 
    {
        parent::__construct();
        $this->_optionTable     = Mage::registry('resources')->getTableName('catalog', 'product_attribute_option');
        $this->_optionTypeTable = Mage::registry('resources')->getTableName('catalog', 'product_attribute_option_type');
    }
    
    /**
     * Get options for attribute values
     *
     * @param   array $params
     * @return  array
     */
    public function getOptions($params)
    {
        $arrRes = array();
        $sql = "SELECT
                    $this->_optionTable.option_id AS value,
                    $this->_optionTable.option_value AS label
                FROM
                    $this->_optionTable,
                    $this->_optionTypeTable
                WHERE
                    $this->_optionTable.option_type_id=$this->_optionTypeTable.option_type_id
                    AND $this->_optionTable.website_id=:website_id
                    AND $this->_optionTypeTable.option_type_code=:option_type";
        $arrParam = array();
        $arrParam['website_id']     = Mage_Core_Environment::getCurentWebsite();
        $arrParam['option_type']    = isset($params['option_type']) ? $params['option_type'] : '';
        
        $arrRes = $this->_read->fetchAll($sql,$arrParam);
        return $arrRes;
    }
    
    /**
     * Insert row in database table
     *
     * @param array $data
     */
    public function insert($data)
    {
        if ($this->_write->insert($this->_optionTable, $data)) {
            return $this->_write->lastInsertId();
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
        $condition = $this->_write->quoteInto('option_id=?', $rowId);
        return $this->_write->update($this->_optionTable, $data, $condition);
    }
    
    /**
     * Delete row from database table
     *
     * @param   int $rowId
     */
    public function delete($rowId)
    {
        $condition = $this->_write->quoteInto('option_id=?', $rowId);
        return $this->_write->delete($this->_optionTable, $condition);
    }
    
    /**
     * Get row from database table
     *
     * @param   int $rowId
     */
    public function getRow($rowId)
    {
        return array();
    }    
}