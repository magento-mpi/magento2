<?php

class Mage_Usa_Model_Shipping_Carrier_Fedex extends Mage_Shipping_Model_Carrier_Abstract
{
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        return $this;
    }
}