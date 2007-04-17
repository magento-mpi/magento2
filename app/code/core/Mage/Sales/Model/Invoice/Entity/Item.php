<?php

class Mage_Sales_Model_Invoice_Entity_Item extends Varien_Data_Object 
{
    public function __construct($data=array())
    {
        parent::__construct($data);
        $this->setEntityType('item');
    }
}