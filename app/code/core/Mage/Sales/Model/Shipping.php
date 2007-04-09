<?php

class Mage_Sales_Model_Shipping
{
    /**
     * Default shipping orig for requests
     *
     * @var array
     */
	protected $_orig = null;
	
	/**
	 * Cached result
	 * 
	 * @var Mage_Sales_Shipping_Quote_Result
	 */
	protected $_result = null;
	
	/**
	 * Reset cached result
	 */
	public function resetResult()
	{
	    $this->_result->reset();
	    return $this;
	}
	
	/**
	 * Constructor
	 *
	 * Initializes $_result object
	 */
	public function __construct()
	{
		$this->_result = new Mage_Sales_Shipping_Quote_Result();
	}
	
	/**
	 * Set shipping orig data
	 */
	public function setOrigData($data)
	{
		$this->_orig = $data;
	}
	
	public function getOrigData()
	{
	    if (!isset($this->_orig)) {
            $this->setOrigData(Mage::getConfig('Mage_Sales')->getShippingOrig());
	    }
	    return $this->_orig;
	}
	
	/**
	 * Retrieve all quotes for supplied shipping data
	 * 
	 * @param Mage_Sales_Shipping_Quote_Request $data
	 * @return Mage_Sales_Shipping_Quote_Result
	 */
	public function fetchQuotes(Mage_Sales_Shipping_Quote_Request $request)
    {
    	if (!$request->getOrig()) {
    		$request->setData($this->getOrigData());
    	}

    	if (!$request->limitVendor) { 
	    	$vendors = Mage::getConfig()->getGlobalCollection('salesShippingVendors')->children();

	        foreach ($vendors as $vendor) {
	            if (!$vendor->is('active')) {
	                continue;
	            }
	            $request->setVendor($vendor->getName());
	            $className = $vendor->getClassName();
	            $obj = new $className();
	            $result = $obj->fetchQuotes($request);
	            $this->_result->append($result);
	        }
    	} else {
    	    $obj = Mage::getConfig()->getGlobalInstance('salesShippingVendors', $request->limitVendor);
    	    $result = $obj->fetchQuotes($request);
    	    $this->_result->append($result);
    	}
    	
        return $this->_result;
    }
}