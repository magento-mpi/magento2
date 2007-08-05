<?php

class Mage_Sales_Model_Shipping_Carrier_Pickup extends Mage_Sales_Model_Shipping_Carrier_Abstract
{
	/**
	 * Enter description here...
	 *
	 * @param Mage_Sales_Model_Shipping_Request $data
	 * @return Mage_Sales_Model_Shipping_Result
	 */
	public function collectRates(Mage_Sales_Model_Shipping_Rate_Request $request)
    {
        $result = Mage::getModel('sales/shipping_rate_result');

        if (!empty($rate)) {
	    	$rate = Mage::getModel('sales/shipping_rate_service');
	    	
	    	$carrier = 'pickup';
	    	$carrierTitle = (string)Mage::getSingleton('sales/config')->getShippingConfig($carrier)->title;
	    	$rate->setCarrier($carrier);
	    	$rate->setCarrierTitle($vendorTitle);
	    	
	    	$rate->setMethod('store');
	    	$rate->setMethodTitle('Store Pickup');
	    	
	    	$rate->setPrice(0);
	    	$rate->setCost(0);
    	
    	    $result->append($rate);
        }
        
    	return $result;
    }
}