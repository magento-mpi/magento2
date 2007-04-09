<?php

class Mage_Sales_Model_Mysql4_Quote_Address extends Mage_Sales_Model_Quote_Address
{
    protected static $_read;
    protected static $_write;
    protected static $_addressTable;
    
    public function __construct($data=array())
    {
        self::$_read = Mage::registry('resources')->getConnection('sales', 'read');
        self::$_write = Mage::registry('resources')->getConnection('sales', 'write');
        self::$_addressTable = Mage::registry('resources')->getTableName('sales', 'quote_address');
    }
    
    public function load($addressId, $loadAttributes=true)
    {
        $select = self::$_read->select()->from(self::$_addressTable)
            ->where(self::$_read->quoteInto("quote_address_id=?", $addressId));
        $this->setData(self::$_read->fetchRow($select));
        
        if ($loadAttributes) { 
            $this->getAttributes()->loadByEntity('address', $addressId);
        }
    }
    
    public function save($saveAttributes=true)
    {
        if (!$this->getQuoteAddressId()) {
            $this->setQuoteAddressId(self::$_write->insert(self::$_addressTable, $this->getData()));
        } else {
            self::$_write->update(self::$_addressTable, $this->getData(), self::$_write->quoteInto('quote_address_id=?', $this->getQuoteAddressId()));
        }
        if ($saveAttributes) {
            $this->getAttributes()->setDataToAll('quote_address_id', $this->getQuoteAddressId())->saveAll();
        }
    }
    
}