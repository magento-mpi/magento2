<?php

class Mage_Sales_Model_Document_Entity_Item extends Varien_Object 
{
    public function __construct($data=array())
    {
        parent::__construct($data);
        $this->setEntityType('item');
    }
    
    public function getPrice()
    {
        $price = $this->getDate('price');
        
        return $price;
    }
}