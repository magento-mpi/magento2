<?php

class Mage_Sales_Shipping_Table extends Mage_Sales_Shipping_Abstract
{
	/**
	 * Enter description here...
	 *
	 * @param Mage_Sales_Shipping_Request $data
	 * @return unknown
	 */
	public function fetchQuotes(Mage_Sales_Shipping_Request $request)
    {
    	$result = new Mage_Sales_Shipping_Quote_Result();
    	
    	$quote = new Mage_Sales_Shipping_Quote();
    	$quote->type = 'table';
    	$quote->method = 'free';
    	$quote->title = 'Free Shipping';
    	$quote->price = 0;
    	
    	$result->append($quote);
        
    	return $result;
    }
}