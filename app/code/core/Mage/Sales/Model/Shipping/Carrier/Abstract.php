<?php

abstract class Mage_Sales_Model_Shipping_Carrier_Abstract
{
    protected $_rates = null;
    
    abstract public function collectRates(Mage_Sales_Model_Shipping_Rate_Request $request);
}