<?php
/**
 * Product attributes options model
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product_Attribute_Option
{
    protected $_read;
    protected $_write;
    protected $_optionTable;
    protected $_optionTypeTable;
    
    public function __construct() 
    {
        $this->_read            = Mage::registry('resources')->getConnection('catalog_read');
        $this->_write           = Mage::registry('resources')->getConnection('catalog_write');
        $this->_optionTable     = Mage::registry('resources')->getTableName('catalog_resource', 'product_attribute_option');
        $this->_optionTypeTable = Mage::registry('resources')->getTableName('catalog_resource', 'product_attribute_option_type');
    }

    public function getOptionValue($optionId)
    {
        $arrRes = array();
        $sql = "SELECT
                    $this->_optionTable.option_value
                FROM
                    $this->_optionTable
                WHERE
                    $this->_optionTable.website_id=:website_id
                    AND $this->_optionTable.option_id=:option_id";
        $arrParam = array();
        $arrParam['website_id']   = Mage::registry('website')->getId();
        $arrParam['option_id']    = $optionId;
        
        $arrRes = $this->_read->fetchOne($sql,$arrParam);
        return $arrRes;
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
        $arrParam['website_id']     = Mage::registry('website')->getId();
        $arrParam['option_type']    = isset($params['option_type']) ? $params['option_type'] : '';
        
        $arrRes = $this->_read->fetchAll($sql,$arrParam);
        return $arrRes;
    }
    
    /**
     * Get options id for attribute values
     *
     * @param   array $params
     * @return  array
     */
    public function getOptionsId($params)
    {
        $arrRes = array();
        $sql = "SELECT
                    $this->_optionTable.option_id 
                FROM
                    $this->_optionTable,
                    $this->_optionTypeTable
                WHERE
                    $this->_optionTable.option_type_id=$this->_optionTypeTable.option_type_id
                    AND $this->_optionTable.website_id=:website_id
                    AND $this->_optionTypeTable.option_type_code=:option_type";
        $arrParam = array();
        $arrParam['website_id']     = Mage::registry('website')->getId();
        $arrParam['option_type']    = isset($params['option_type']) ? $params['option_type'] : '';
        
        $arrRes = $this->_read->fetchCol($sql,$arrParam);
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
        $condition = $this->_write->quoteInto('option_id=?', $rowId);
        return self::$_write->update($this->_optionTable, $data, $condition);
    }
    
    /**
     * Delete row from database table
     *
     * @param   int $rowId
     */
    public function delete($rowId)
    {
        $condition = $this->_write->quoteInto('option_id=?', $rowId);
        return self::$_write->delete($this->_optionTable, $condition);
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