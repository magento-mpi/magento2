<?php

abstract class Mage_Shiptable_Model_Table extends Mage_Sales_Model_Shipping_Vendor_Abstract
{
    protected $_conditionName = 'package_weight';
    
    public function setConditionName($name)
    {
        $this->_conditionName = $name;
    }
    
	/**
	 * Enter description here...
	 *
	 * @param Mage_Sales_Model_Shipping_Request $data
	 * @return Mage_Sales_Model_Shipping_Result
	 */
	public function fetchServices(Mage_Sales_Model_Shipping_Method_Request $request)
    {
        if (!$request->getConditionName()) {
            $request->setConditionName($this->_conditionName);
        }

        $result = Mage::getModel('sales_model', 'shipping_method_result');

        $rate = $this->getRate($request);
        if (!empty($rate)) {
	    	$quote = Mage::getModel('sales_model', 'shipping_method_service');
	    	
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
    
    public function getRate(Mage_Sales_Model_Shipping_Method_Request $request)
    {
        
    }
}