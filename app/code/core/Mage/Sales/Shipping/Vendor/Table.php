<?php

class Mage_Sales_Shipping_Vendor_Table extends Mage_Sales_Shipping_Vendor_Abstract
{
	/**
	 * Enter description here...
	 *
	 * @param Mage_Sales_Shipping_Request $data
	 * @return Mage_Sales_Shipping_Result
	 */
	public function fetchQuotes(Mage_Sales_Shipping_Request $request)
    {
    	$result = new Mage_Sales_Shipping_Quote_Result();
    	
    	$quote = new Mage_Sales_Shipping_Quote_Method();
    	$quote->vendor = 'table';
    	$quote->method = 'free';
    	$quote->title = 'Free Shipping';
    	$quote->price = 0;
    	$quote->cost = 0;
    	
    	$result->append($quote);
        
    	return $result;
    }
}