<?php

class Mage_Sales_Model_Shipping_Vendor_Pickup extends Mage_Sales_Model_Shipping_Vendor_Abstract
{
	/**
	 * Enter description here...
	 *
	 * @param Mage_Sales_Model_Shipping_Request $data
	 * @return Mage_Sales_Model_Shipping_Result
	 */
	public function collectMethods(Mage_Sales_Model_Shipping_Method_Request $request)
    {
        $result = Mage::getModel('sales/shipping_method_result');

        if (!empty($rate)) {
	    	$quote = Mage::getModel('sales/shipping_method_service');
	    	
	    	$vendor = 'pickup';
	    	$vendorTitle = (string)Mage::getSingleton('sales/config')->getShippingConfig($vendor)->title;
	    	$quote->setVendor($vendor);
	    	$quote->setVendorTitle($vendorTitle);
	    	
	    	$quote->setService('store');
	    	$quote->setServiceTitle('Store Pickup');
	    	
	    	$quote->setPrice(0);
	    	$quote->setCost(0);
    	
    	    $result->append($quote);
        }
        
    	return $result;
    }
}