<?php

class Mage_Sales_Shipping
{
	protected $_origin = null;
	protected $_result = null;
	
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
	
	/**
	 * Retrieve all quotes for supplied shipping data
	 * 
	 * @param Mage_Sales_Shipping_Quote_Request $data
	 * @return Mage_Sales_Shipping_Quote_Result
	 */
	public function fetchQuotes(Mage_Sales_Shipping_Quote_Request $request)
    {
    	if (!$request->origin) {
    		$request->origin = $this->_origin;
    	}
    	
        $types = Mage::getConfig()->getGlobalConfig('shipping');
        foreach ($types as $type) {
            $className = (string)$type->class;
            $obj = new $className();
            $result = $obj->fetchQuotes($request);
            $this->_result->append($result);
        }
        return $this->_result;
    }
    
    /**
     * Retrieve specific quote by type and optionally method
     * 
     * @param Mage_Sales_Shipping_Quote_Request $$request
     * @param string $type
     * @param string $method
     * @return Mage_Sales_Shipping_Quote_Result|boolean
     */
    public function fetchQuoteByType(Mage_Sales_Shipping_Quote_Request $request, $type, $method='')
    {
    	if (!$request->origin) {
    		$request->origin = $this->_origin;
    	}
    	
    	$className = Mage::getConfig()->getGlobalInstance('shipping', $type);
    	$obj = new $className();
    	$result = $obj->fetchQuotes($request);
    	
    	if (''===$method) {
    		return $result;
    	} else {
    		$quotes = $result->getAllQuotes();
    		foreach ($quotes as $quote) {
    			if ($quote->method===$method) {
    				$result1 = new Mage_Sales_Shipping_Quote_Result();
    				$result1->append($quote);
    				return $result1;
    			}
    		}
    	}
    	return false;
    }
}