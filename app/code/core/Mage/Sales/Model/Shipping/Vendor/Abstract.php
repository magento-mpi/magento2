<?php

abstract class Mage_Sales_Model_Shipping_Vendor_Abstract
{
    protected $_quotes = null;
    
    abstract public function collectMethods(Mage_Sales_Model_Shipping_Method_Request $request);
}