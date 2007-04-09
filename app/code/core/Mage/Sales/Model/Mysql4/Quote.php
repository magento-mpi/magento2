<?php

class Mage_Sales_Model_Mysql4_Quote extends Mage_Sales_Model_Quote
{
    protected static $_read;
    protected static $_write;
    protected static $_quoteTable;
    
    public function __construct($data=array())
    {
        self::$_read = Mage::registry('resources')->getConnection('sales', 'read');
        self::$_write = Mage::registry('resources')->getConnection('sales', 'write');
        self::$_quoteTable = Mage::registry('resources')->getTableName('sales', 'quote');
    }
    
    public function load($quoteId, $loadAttributes=true)
    {
        $quoteTable = Mage::registry('resources')->getTableName('sales', 'quote');
        $select = self::$_read->select()->from($quoteTable)
            ->where(self::$_read->quoteInto("quote_id=?", $quoteId));
        $this->setData(self::$_read->fetchRow($select));

        if ($loadAttributes) {
            $this->loadAttributes();
        }
        
        return $this;
    }
    
    public function loadAttributes()
    {
        $this->getAddresses()->loadByQuoteId($quoteId);
        $this->getItems()->loadByQuoteId($quoteId);
        $this->getPayments()->loadByQuoteId($quoteId);
        $this->getAttributes()->loadByQuoteId($quoteId);
        $this->_distributeAttributes();
        return $this;
    }
        
    public function save($saveAttributes=true)
    {
        if (!$this->getQuoteId()) {
            $this->setQuoteId(self::$_write->insert(self::$_quoteTable, $this->getData()));
        } else {
            self::$_write->update(self::$_quoteTable, $this->getData(), self::$_write->quoteInto('quote_id=?', $this->getQuoteId()));
        }
        if ($saveAttributes) {
            $this->getAddresses()->setDataToAll('quote_id', $this->getQuoteId())->walk('save', array(false));
            $this->getItems()->setDataToAll('quote_id', $this->getQuoteId())->walk('save', array(false));
            $this->getPayments()->setDataToAll('quote_id', $this->getQuoteId())->walk('save', array(false));
            $this->getAttributes()->setDataToAll('quote_id', $this->getQuoteId())->walk('save');
        }
        return $this;
    }
    
    public function delete()
    {
        self::$_write->delete(self::$_quoteTable, self::$_write->quoteInto('quote_id=?', $this->getQuoteId()));
        $this->getAddresses()->walk('delete');
        $this->getItems()->walk('delete');
        $this->getPayments()->walk('delete');
        $this->getAttributes()->walk('delete');
        return $this;
    }
    
    protected function _distributeAttributes()
    {
        foreach ($this->getAttributes()->getItems() as $attr) {
            switch ($attr->getEntityType()) {
                case 'address':
                    $this->getAddresses()->getByAddressId($attr->getEntityId())->addAttribute($attr);
                    break;
                    
                case 'item':
                    $this->getItems()->getByItemId($attr->getEntityId())->addAttribute($attr);
                    break;
                    
                case 'payment':
                    $this->getPayments()->getByPaymentId($attr->getEntityId())->addAttribute($attr);
                    break;
            }
        }
        return $this;
    }

}