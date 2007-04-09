<?php

class Mage_Sales_Resource_Model_Mysql4_Quote_Item extends Mage_Sales_Quote_Item
{
    protected static $_read;
    protected static $_write;
    protected static $_itemTable;
    
    public function __construct($data=array())
    {
        self::$_read = Mage::registry('resources')->getConnection('sales', 'read');
        self::$_write = Mage::registry('resources')->getConnection('sales', 'write');
        self::$_addressTable = Mage::registry('resources')->getTableName('sales', 'quote_item');
    }
    
    public function load($itemId, $loadAttributes=true)
    {
        $select = self::$_read->select()->from(self::$_itemTable)
            ->where(self::$_read->quoteInto("quote_item_id=?", $itemId));
        $this->setData(self::$_read->fetchRow($select));
        
        if ($loadAttributes) {
            $this->getAttributes()->loadByEntity('item', $itemId);
        }
    }

    public function save($saveAttributes=true)
    {
        if (!$this->getQuoteItemId()) {
            $this->getQuoteItemId(self::$_write->insert(self::$_itemTable, $this->getData()));
        } else {
            self::$_write->update(self::$_itemTable, $this->getData(), self::$_write->quoteInto('quote_item_id=?', $this->getQuoteItemId()));
        }
        if ($saveAttributes) {
            $this->getAttributes()->setDataToAll('quote_item_id', $this->getQuoteItemId())->saveAll();
        }
    }
    
}