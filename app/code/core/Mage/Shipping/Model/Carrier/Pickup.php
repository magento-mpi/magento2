<?php

class Mage_Shipping_Model_Carrier_Pickup extends Mage_Shipping_Model_Carrier_Abstract
{
	/**
	 * Enter description here...
	 *
	 * @param Mage_Shipping_Model_Rate_Request $data
	 * @return Mage_Shipping_Model_Rate_Result
	 */
	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!Mage::getStoreConfig('carriers/pickup/active')) {
            return false;
        }
        
        $result = Mage::getModel('shipping/rate_result');

        if (!empty($rate)) {
	    	$method = Mage::getModel('shipping/rate_result_method');
	    	
	    	$carrier = 'pickup';
	    	$carrierTitle = Mage::getStoreConfig('carriers/pickup/title');
	    	$method->setCarrier($carrier);
	    	$method->setCarrierTitle($carrierTitle);
	    	
	    	$method->setMethod('store');
	    	$method->setMethodTitle('Store Pickup');
	    	
	    	$method->setPrice(0);
	    	$method->setCost(0);
    	
    	    $result->append($method);
        }
        
    	return $result;
    }
}