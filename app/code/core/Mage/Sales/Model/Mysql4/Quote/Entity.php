<?php

class Mage_Sales_Model_Mysql4_Quote_Entity extends Mage_Sales_Model_Quote_Entity
{
    protected static $_read;
    protected static $_write;
    protected static $_entityTable;
    
    public function __construct($data=array())
    {
        parent::__construct($data);
        self::$_read = Mage::registry('resources')->getConnection('sales_read');
        self::$_write = Mage::registry('resources')->getConnection('sales_write');
        self::$_entityTable = Mage::registry('resources')->getTableName('sales', 'quote_entity');
    }
    
    public function load($entityId)
    {
        $condition = self::$_read->quoteInto("quote_entity_id=?", $entityId);
        $select = self::$_read->select()->from(self::$_entityTable)->where($condition);
        $this->setData(self::$_read->fetchRow($select));
        $this->resetChanged(false);
    }
    
    public function save()
    {
        if ($this->getDeleteFlag()) {
            return $this->delete();
        }
        
        if ($this->isChanged()) {
        
            $this->setQuoteId($this->getQuote()->getQuoteId());
            
            if ($this->getQuoteEntityId()<=0) {
                if (self::$_write->insert(self::$_entityTable, $this->getData())) { 
                    $this->setQuoteEntityId(self::$_write->lastInsertId());
                }
            } else {
                $condition = self::$_write->quoteInto('quote_entity_id=?', $this->getQuoteEntityId());
                self::$_write->update(self::$_entityTable, $this->getData(), $condition);
            }
            $this->resetChanged(false);
        }
        
        foreach ($this->_attributes as $attr) {
            $attr->save();
        }

        return $this;
    }
    
    public function delete()
    {
        foreach ($this->_attributes as $attr) {
            $attr->delete();
        }
        $condition = self::$_write->quoteInto('quote_entity_id=?', $this->getQuoteEntityId());
        self::$_write->delete(self::$_entityTable, $condition);
        return $this;
    }
    
}