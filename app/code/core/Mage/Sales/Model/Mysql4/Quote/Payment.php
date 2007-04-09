<?php

class Mage_Sales_Model_Mysql4_Quote_Payment extends Mage_Sales_Quote_Payment
{
    protected static $_read;
    protected static $_write;
    protected static $_paymentTable;
    
    public function __construct($data=array())
    {
        self::$_read = Mage::registry('resources')->getConnection('sales', 'read');
        self::$_write = Mage::registry('resources')->getConnection('sales', 'write');
        self::$_paymentTable = Mage::registry('resources')->getTableName('sales', 'quote_payment');
    }
    
    public function load($paymentId, $loadAttributes=true)
    {
        $select = self::$_read->select()->from(self::$_paymentTable)
            ->where(self::$_read->quoteInto("quote_payment_id=?", $paymentId));
        $this->setData(self::$_read->fetchRow($select));
        
        if ($loadAttributes) {
            $this->getAttributes()->loadByEntity('address', $paymentId);
        }
    }
    
    public function save($saveAttributes=true)
    {
        if (!$this->getQuotePaymentId()) {
            $this->getQuotePaymentId(self::$_write->insert(self::$_paymentTable, $this->getData()));
        } else {
            self::$_write->update(self::$_paymentTable, $this->getData(), self::$_write->quoteInto('quote_payment_id=?', $this->getQuotePaymentId()));
        }
        if ($saveAttributes) {
            $this->getAttributes()->setDataToAll('quote_payment_id', $this->getQuotePaymentId())->saveAll();
        }
    }
}