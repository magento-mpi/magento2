<?php
/**
 * Product attribute model
 *
 * @package    Mage
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
        $this->_attributeTable = Mage::getSingleton('core/resource')->getTableName('catalog_resource', 'product_attribute');
        $this->_read = Mage::getSingleton('core/resource')->getConnection('catalog_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('catalog_write');
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
        $data = $this->_prepareSaveData($attribute);
        $this->_write->beginTransaction();
        
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
            throw $e;
        }
    }
    
    protected function _prepareSaveData(Mage_Catalog_Model_Product_Attribute $attribute)
    {
        $data = array(
            'attribute_code'  => $attribute->getAttributeCode(),
            'data_input'      => $attribute->getDataInput(),
            'data_saver'      => $attribute->getDataSaver(),
            'data_source'     => $attribute->getDataSource(),
            'data_type'       => $attribute->getDataType(),
            'validation'      => $attribute->getValidation(),
            'input_format'    => $attribute->getInputFormat(),
            'output_format'   => $attribute->getOutputFormat(),
            'required'        => (int) (bool) $attribute->getRequired(),
            'searchable'      => (int) (bool) $attribute->getSearchable(),
            'comparable'      => (int) (bool) $attribute->getComparable(),
            'multiple'        => (int) (bool) $attribute->getMultiple(),
            'deletable'       => (int) (bool) $attribute->getDeletable(),
        );
        
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