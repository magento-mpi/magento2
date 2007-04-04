<?php
/**
 * Product attribute value abstract model
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
abstract class Mage_Catalog_Resource_Model_Mysql4_Product_Attribute_Abstract extends Mage_Catalog_Resource_Model_Mysql4 implements Mage_Core_Resource_Model_Db_Table_Interface 
{
    protected $_attributeValueTable;

    /**
     * Insert row in database table
     *
     * @param   array $data
     * @return  int || false
     */
    public function insert($data)
    {
        if (empty($data['website_id'])) {
            $data['website_id'] = Mage::registry('website')->getId();
        }
        if ($this->_write->insert($this->_attributeValueTable, $data)) {
            return $this->_write->lastInsertId();
        }
        return false;
    }
    
    protected function _renderFkCondition($arrId)
    {
        if (is_array($arrId)) {
            if (!empty($arrId['value_id'])) {
                $condition = $this->_write->quoteInto('value_id=?', $arrId['value_id']);
            }
            else {
                if (empty($arrId['attribute_id'])) {
                    Mage::exception('"attribute_id" can not be empty', 0, 'Mage_Catalog');
                }
                if (empty($arrId['product_id'])) {
                    Mage::exception('"product_id" can not be empty', 0, 'Mage_Catalog');
                }
                if (empty($arrId['website_id'])) {
                    $arrId['website_id'] = Mage::registry('website')->getId();
                }
                
                $condition = $this->_write->quoteInto('product_id=?', $arrId['product_id']).
                             ' AND ' . $this->_write->quoteInto('attribute_id=?', $arrId['attribute_id']).
                             ' AND ' . $this->_write->quoteInto('website_id=?', $arrId['website_id']);
            }            
        }
        else {
            $condition = $this->_write->quoteInto('value_id=?', $arrId);
        }
        
        return $condition;
    }
    
    /**
     * Update row in database table
     *
     * @param   array $data
     * @param   array $rowId
     * @return  int
     */
    public function update($data, $arrId)
    {
        $condition = $this->_renderFkCondition($arrId);
        return $this->_write->update($this->_attributeValueTable, $data, $condition);
    }
    
    /**
     * Delete row from database table
     *
     * @param   int $rowId
     */
    public function delete($arrId)
    {
        $condition = $this->_renderFkCondition($arrId);
        return $this->_write->delete($this->_attributeValueTable, $condition);
    }
    
    /**
     * Get row from database table
     *
     * @param   int $rowId
     */
    public function getRow($arrId)
    {
        $sql = "SELECT * FROM $this->_attributeValueTable WHERE " . $this->_renderFkCondition($arrId);
        return $this->_read->fetchRow($sql);
    }    
}