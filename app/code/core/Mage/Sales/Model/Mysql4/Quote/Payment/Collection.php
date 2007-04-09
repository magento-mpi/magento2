<?php

class Mage_Sales_Model_Mysql4_Quote_Payment_Collection extends Varien_Data_Collection_Db
{
    static protected $_paymentTable = null;

    public function __construct() 
    {
        parent::__construct(Mage::registry('resources')->getConnection('sales_read'));
        self::$_paymentTable = Mage::registry('resources')->getTableName('sales', 'quote_payment');
        $this->_sqlSelect->from(self::$_paymentTable);
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('sales', 'quote_payment'));
    }
    
    public function loadByQuoteId($quoteId)
    {
        $this->addFilter('quote_id', (int)$quoteId, 'and');
        $this->load();
        return $this;
    }    
    
    public function getByPaymentId($paymentId)
    {
        foreach ($this->getItems() as $item) {
            if ($item->getQuotePaymentId()==$paymentId) {
                return $item;
            }
        }
    }
}