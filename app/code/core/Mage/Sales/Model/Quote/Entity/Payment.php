<?php

class Mage_Sales_Model_Quote_Entity_Payment extends Mage_Customer_Model_Payment
{
    public function __construct($data=array())
    {
        parent::__construct($data);
        $this->setEntityType('payment');
    }
}