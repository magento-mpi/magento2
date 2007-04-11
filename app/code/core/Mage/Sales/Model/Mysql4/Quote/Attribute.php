<?php

class Mage_Sales_Model_Mysql4_Quote_Attribute extends Mage_Sales_Model_Quote_Attribute
{
    protected static $_read;
    protected static $_write;
    protected static $_attrTable;
    
    public function __construct($data=array())
    {
        parent::__construct($data);
        self::$_read = Mage::registry('resources')->getConnection('sales_read');
        self::$_write = Mage::registry('resources')->getConnection('sales_write');
        self::$_attrTable = Mage::registry('resources')->getTableName('sales', 'quote_attribute');
    }
    
    public function load($attributeId)
    {
        $condition = self::$_read->quoteInto("quote_address_id=?", $attributeId);
        $select = self::$_read->select()->from(self::$_attrTable)->where($condition);
        $this->setData(self::$_read->fetchRow($select));
        $this->resetChanged(false);
    }
    
    public function save()
    {
        if ($this->getDeleteFlag()) {
            return $this->delete();
        }

        if (!$this->isChanged()) {
            return $this;
        }
        
        $this->setQuoteId($this->getEntity()->getQuoteId());
        $this->setQuoteEntityId($this->getEntity()->getQuoteEntityId());
        
        if ($this->getQuoteAttributeId()<=0) {
            if (self::$_write->insert(self::$_attrTable, $this->getData())) { 
                $this->setQuoteAttributeId(self::$_write->lastInsertId());
            }
        } else {
            $condition = self::$_write->quoteInto('quote_attribute_id=?', $this->getQuoteAttributeId());
            self::$_write->update(self::$_attrTable, $this->getData(), $condition);
        }
        $this->resetChanged(false);

        return $this;
    }
    
    public function delete()
    {
        $condition = self::$_write->quoteInto('quote_attribute_id=?', $this->getQuoteAttributeId());
        self::$_write->delete(self::$_attrTable, $condition);
        return $this;
    }
    
}