<?php

class Mage_Sales_Model_Quote_Payment extends Varien_Data_Object 
{
    protected $_attributes = array();
    
    public function getAttributes()
    {
        if (is_null($this->_attributes)) {
            $this->_attributes = Mage::getModel('sales', 'quote_attribute_collection');
        }
        return $this->_attributes;
    }
    
    public function addAttribute(Mage_Sales_Model_Quote_Attribute $attribute)
    {
        $this->getAttributes()->addItem($attribute);
    }
}