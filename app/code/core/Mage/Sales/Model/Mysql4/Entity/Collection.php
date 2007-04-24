<?php

class Mage_Sales_Model_Mysql4_Entity_Collection extends Varien_Data_Collection_Db
{
    protected $_documentTable;
    protected $_attributeTable;
    
    public function __construct() 
    {
        parent::__construct(Mage::registry('resources')->getConnection('sales_read'));
    }
    
    public function setDocType($docType)
    {
        $this->_documentTable = Mage::registry('resources')->getTableName('sales_resource', $docType);
        $this->_idField = $docType.'_id';
        $this->_attributeTable = Mage::registry('resources')->getTableName('sales_resource', $docType.'_attribute');
        
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('sales', $docType));
    }
    
}