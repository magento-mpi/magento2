<?php

class Mage_Sales_Model_Mysql4_Invoice extends Mage_Sales_Model_Mysql4_Document
{
    public function __construct($data=array())
    {
        parent::__construct();
        $this->setDocType('invoice');
    }
    
}