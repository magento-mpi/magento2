<?php

abstract class Mage_Shipping_Model_Carrier_Abstract extends Varien_Object
{
    protected $_rates = null;
    
    abstract public function collectRates(Mage_Shipping_Model_Rate_Request $request);
}