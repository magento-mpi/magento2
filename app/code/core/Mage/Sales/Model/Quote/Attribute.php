<?php

class Mage_Sales_Model_Quote_Attribute
{
    function collectTotals(Mage_Sales_Model_Quote $quote)
    {
        return $this;
    }
    
    function getTotals(Mage_Sales_Model_Quote $quote)
    {
        return array();
    }
}