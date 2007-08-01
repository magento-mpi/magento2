<?php

class Mage_Sales_Model_Mysql4_Quote_Collection extends Mage_Sales_Model_Mysql4_Document_Collection 
{
    public function __construct()
    {
        parent::__construct();
        $this->setDocType('quote');
    }
}