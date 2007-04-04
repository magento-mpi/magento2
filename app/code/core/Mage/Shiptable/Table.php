<?php

class Mage_Shiptable_Table extends Mage_Sales_Shipping_Vendor_Abstract
{
    protected $_conditionName = 'packageWeight';
    
    public function setConditionName($name)
    {
        $this->_conditionName = $name;
    }
    
	/**
	 * Enter description here...
	 *
	 * @param Mage_Sales_Shipping_Request $data
	 * @return Mage_Sales_Shipping_Result
	 */
	public function fetchQuotes(Mage_Sales_Shipping_Request $request)
    {
        if (!$request->conditionName) {
            $request->conditionName = $this->_conditionName;
        }
        
        $rate = Mage::getModel('shiptable', 'table')->getRate($request);

        $result = new Mage_Sales_Shipping_Quote_Result();

    	$quote = new Mage_Sales_Shipping_Quote_Method();
    	$quote->vendor = 'shiptable';
    	$quote->method = 'table';
    	$quote->title = 'Table rate';
    	$quote->price = $rate['price'];
    	$quote->cost = $rate['cost'];
    	
    	$result->append($quote);
        
    	return $result;
    }
}