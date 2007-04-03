<?php

abstract class Mage_Sales_Shipping_Vendor_Abstract
{
    protected $_quotes = null;
    
    abstract public function fetchQuotes(Mage_Sales_Shipping_Request $request);
}