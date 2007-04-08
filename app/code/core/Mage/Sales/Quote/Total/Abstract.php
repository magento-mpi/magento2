<?php

abstract class Mage_Sales_Quote_Total_Abstract
{
    protected $_quote = null;
    
    public function __construct($quote)
    {
        $this->_quote = $quote;
    }
}