<?php

class Mage_Shiptable_Model_Table extends Mage_Sales_Model_Shipping_Carrier_Abstract
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
	public function collectRates(Mage_Sales_Model_Shipping_Rate_Request $request)
    {
        if (!$request->getConditionName()) {
            $request->setConditionName($this->_conditionName);
        }

        $result = Mage::getModel('sales/shipping_rate_result');

        $rate = $this->getRate($request);
        if (!empty($rate)) {
	    	$method = Mage::getModel('sales/shipping_rate_result_method');
	    	
	    	$carrier = 'shiptable';
	    	$carrierTitle = (string)Mage::getSingleton('sales/config')->getShippingConfig($carrier)->title;
	    	$method->setVendor($carrier);
	    	$method->setVendorTitle($carrierTitle);
	    	
	    	$method->setService('best');
	    	$method->setServiceTitle('Best way');
	    	
	    	$method->setPrice($rate['price']);
	    	$method->setCost($rate['cost']);
    	
    	    $result->append($method);
        }
        
    	return $result;
    }
    
    public function getRate(Mage_Sales_Model_Shipping_Rate_Request $request)
    {
        return Mage::getResourceModel('shiptable/table')->getRate($request);
    }
}