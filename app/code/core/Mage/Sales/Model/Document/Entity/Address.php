<?php

class Mage_Sales_Model_Document_Entity_Address extends Mage_Customer_Model_Address 
{
    public function __construct($data=array())
    {
        parent::__construct($data);
        $this->setEntityType('address');
    }

}