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
        $this->_optionTable     = $this->getTableName('catalog_read', 'product_attribute_option');
        $this->_optionTypeTable = $this->getTableName('catalog_read', 'product_attribute_option_type');
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