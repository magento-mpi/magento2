<?php
/**
 * Product attribute model
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product_Attribute
{
    protected $_read;
    protected $_write;

    protected $_attributeTable;
    
    public function __construct()
    {
        $this->_attributeTable = Mage::registry('resources')->getTableName('catalog_resource', 'product_attribute');
        $this->_read = Mage::registry('resources')->getConnection('catalog_read');
        $this->_write = Mage::registry('resources')->getConnection('catalog_write');
    }
    
    public function load($attributeId)
    {
        $sql = "SELECT * FROM $this->_attributeTable WHERE attribute_id=:attribute_id";
        return $this->_read->fetchRow($sql, array('attribute_id'=>$attributeId));
    }    

    public function loadByCode($attributeCode)
    {
        $sql = "SELECT * FROM $this->_attributeTable WHERE attribute_code=:attribute_code";
        return $this->_read->fetchRow($sql, array('attribute_code'=>$attributeCode));
    }
    
    public function save(Mage_Catalog_Model_Product_Attribute $attribute)
    {
        $data = $this->_prepareSaveData();
        $this->_write->beginTrabsaction();
        
        try {
            if ($attribute->getId()) {
                $condition = $this->_write->quoteInto('attribute_id=?', $attribute->getId());
                $this->_write->update($this->_attributeTable, $data, $condition);
            }
            else {
                $this->_write->insert($this->_attributeTable, $data);
                $attribute->setAttributeId($this->_write->lastInsertId());
            }
            
            $this->_write->commit();
        }
        catch (Exception $e){
            $this->_write->rollBack();
        }
    }
    
    protected function _prepareSaveData(Mage_Catalog_Model_Product_Attribute $attribute)
    {
        /*$data = array(
        );*/
        $data = $attribute->getData();
        return $data;
    }
    
    public function delete($atrtibuteId)
    {
        $condition = $this->_write->quoteInto('attribute_id=?', $atrtibuteId);
        $this->_write->beginTransaction();
        try {
            $this->_write->delete($this->_attributeTable, $condition);
            $this->_write->commit();
        }
        catch (Exception $e)
        {
            $this->_write->rollBack();
            throw $e;
        }
    }
}