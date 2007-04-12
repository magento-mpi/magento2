<?php

class Mage_Sales_Model_Mysql4_Quote extends Mage_Sales_Model_Quote
{
    protected static $_read;
    protected static $_write;
    protected static $_quoteTable;
    
    public function __construct($data=array())
    {
        parent::__construct($data);
        self::$_read = Mage::registry('resources')->getConnection('sales_read');
        self::$_write = Mage::registry('resources')->getConnection('sales_write');
        self::$_quoteTable = Mage::registry('resources')->getTableName('sales', 'quote');
    }
    
    public function load($quoteId)
    {
        $quoteTable = Mage::registry('resources')->getTableName('sales', 'quote');
        $select = self::$_read->select()->from($quoteTable)
            ->where(self::$_read->quoteInto("quote_id=?", $quoteId));
        $rowData = self::$_read->fetchRow($select);
        if (empty($rowData)) {
            return $this;
        }
        $this->setData($rowData);

        $this->_entities->loadByQuoteId($quoteId);
        $this->_attributes->loadByQuoteId($quoteId);
        
        $this->_afterLoad();
        
        return $this;
    }
        
    public function save()
    {
        $createdAt = $this->getQuoteEntity()->getAttribute('created_at');
        if (empty($createdAt) || '0000-00-00 00:00:00'==$createdAt) {
            $this->resetChanged(true);
            $this->getQuoteEntity()->setAttribute('created_at', new Zend_Db_Expr('now()'));
        }
        
        if ($this->isChanged()) {
            if (!$this->getQuoteId()) {
                if (self::$_write->insert(self::$_quoteTable, $this->getData())) {
                    $this->setQuoteId(self::$_write->lastInsertId());
                }
            } else {
                $condition = self::$_write->quoteInto('quote_id=?', $this->getQuoteId());
                self::$_write->update(self::$_quoteTable, $this->getData(), $condition);
            }
            $this->resetChanged(false);
        }
        
        $this->getEntities()->walk('save');
        $this->getAttributes()->walk('save');

        return $this;
    }
    
    public function delete()
    {
        $this->_entities->walk('delete');
        $this->_attributes->walk('delete');
        self::$_write->delete(self::$_quoteTable, self::$_write->quoteInto('quote_id=?', $this->getQuoteId()));
        return $this;
    }

}