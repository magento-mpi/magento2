<?php

abstract class Mage_Sales_Shipping_Abstract
{
    protected $_quotes = null;
    
    abstract public function fetchQuotes($data, $refresh=false);
}