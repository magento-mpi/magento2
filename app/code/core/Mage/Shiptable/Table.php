<?php

class Mage_Shiptable_Table extends Mage_Sales_Shipping_Vendor_Abstract
{
    protected $_conditionName = 'package_weight';
    
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
	public function fetchQuotes(Mage_Sales_Shipping_Quote_Request $request)
    {
        if (!$request->getConditionName()) {
            $request->setConditionName($this->_conditionName);
        }

        $result = new Mage_Sales_Shipping_Quote_Result();

        $rate = Mage::getResourceModel('shiptable', 'table')->getRate($request);
        if (!empty($rate)) {
	    	$quote = new Mage_Sales_Shipping_Quote_Method();
	    	
	    	$quote->setVendor('shiptable');
	    	$quote->setVendorTitle('Table rate');
	    	
	    	$quote->setMethod('shiptable_rate');
	    	$quote->setMethodTitle('Table rate');
	    	
	    	$quote->setPrice($rate['price']);
	    	$quote->setCost($rate['cost']);
    	
    	    $result->append($quote);
        }
        
    	return $result;
    }
}