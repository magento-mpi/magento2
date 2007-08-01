<?php

class Mage_Sales_Model_Order_Entity_Item extends Varien_Object 
{
    public function __construct($data=array())
    {
        parent::__construct($data);
        $this->setEntityType('item');
    }
}