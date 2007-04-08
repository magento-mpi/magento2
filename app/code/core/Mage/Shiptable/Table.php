<?php

abstract class Mage_Shiptable_Table extends Mage_Sales_Shipping_Vendor_Abstract
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

        $rate = $this->getRate($request);
        if (!empty($rate)) {
	    	$quote = new Mage_Sales_Shipping_Quote_Service();
	    	
	    	$vendor = 'shiptable';
	    	$vendorTitle = (string)Mage::getConfig('Mage_Sales')->getShippingDefaults($vendor)->title;
	    	$quote->setVendor($vendor);
	    	$quote->setVendorTitle($vendorTitle);
	    	
	    	$quote->setService('best');
	    	$quote->setServiceTitle('Best way');
	    	
	    	$quote->setPrice($rate['price']);
	    	$quote->setCost($rate['cost']);
    	
    	    $result->append($quote);
        }
        
    	return $result;
    }
    
    public function getRate(Mage_Sales_Shipping_Quote_Request $request)
    {
        
    }
}