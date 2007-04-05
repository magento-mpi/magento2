<?php

class Mage_Sales_Shipping
{
    /**
     * Default shipping origin for requests
     *
     * @var array
     */
	protected $_origin = null;
	
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
	 * Set shipping origin data
	 */
	public function setOriginData($data)
	{
		$this->_origin = $data;
	}
	
	public function getOriginData()
	{
	    if (!isset($this->_origin)) {
            $this->setOriginData(Mage::getConfig('Mage_Sales')->getShippingOrigin());
	    }
	    return $this->_origin;
	}
	
	/**
	 * Retrieve all quotes for supplied shipping data
	 * 
	 * @param Mage_Sales_Shipping_Quote_Request $data
	 * @return Mage_Sales_Shipping_Quote_Result
	 */
	public function fetchQuotes(Mage_Sales_Shipping_Quote_Request $request)
    {
    	if (!$request->getOrigin()) {
    		$request->setData($this->getOriginData());
    	}

    	if (!$request->limitVendor) { 
	    	$types = Mage::getConfig()->getGlobalConfig('salesShippingVendors')->children();

	        foreach ($types as $type) {
	            if ('true'!==(string)$type->active) {
	                continue;
	            }
	            $className = (string)$type->class;
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