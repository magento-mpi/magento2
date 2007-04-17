<?php

class Mage_Sales_Model_Order_Entity_Status extends Varien_Data_Object 
{
    public function __construct($data=array())
    {
        parent::__construct($data);
        $this->setEntityType('status');
    }
}