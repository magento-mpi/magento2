<?php
/**
 * Product attribute factory
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product_Attribute_Factory implements Mage_Core_Model_Db_Table_Interface 
{
    protected function _getModel($dataType)
    {
        $model_name = 'product_attribute_' . $dataType;
        return Mage::getModel('catalog', $model_name);
    }
    
    /**
     * Insert row in database table
     *
     * @param   array $data
     * @return  int || false
     */
    public function insert($data, $dataType)
    {
        return $this->_getModel($dataType)->insert($data);
    }
    
    /**
     * Update row in database table
     *
     * @param   array $data
     * @param   int   $rowId
     * @return  int
     */
    public function update($data, $rowId, $dataType)
    {
        return $this->_getModel($dataType)->update($data, $rowId);
    }
    
    /**
     * Delete row from database table
     *
     * @param   int $rowId
     */
    public function delete($rowId,$dataType)
    {
        return $this->_getModel($dataType)->delete($rowId);
    }
    
    /**
     * Get row from database table
     *
     * @param   int $rowId
     */
    public function getRow($rowId, $dataType)
    {
        return $this->_getModel($dataType)->getRow($data);
    }    
}