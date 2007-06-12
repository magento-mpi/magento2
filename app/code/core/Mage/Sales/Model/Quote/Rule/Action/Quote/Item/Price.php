<?php

class Mage_Sales_Model_Quote_Rule_Action_Quote_Item_Price extends Mage_Rule_Model_Action_Abstract 
{
    public function toArray(array $arrAttributes=array())
    {
        
    }
    
    public function toString($format='')
    {
        $str = "Update item price";
    }
    
}