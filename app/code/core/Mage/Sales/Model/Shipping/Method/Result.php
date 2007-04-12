<?php

class Mage_Sales_Model_Shipping_Method_Result
{
	protected $_methods = array();
	protected $_error = null;
	
	/**
	 * Reset result
	 */
	public function reset()
	{
	    $this->_methods = array();
	    return $this;
	}
	
	public function setError($error)
	{
	    $this->_error = $error;
	}
	
	public function getError()
	{
	    return $this->_error;
	}
	
	/**
	 * Add a quote to the result
	 *
	 * @param Mage_Sales_Model_Shipping_Method_Service|Mage_Sales_Model_Shipping_Method_Result $result
	 */
	public function append($result)
	{
		if ($result instanceof Mage_Sales_Model_Shipping_Method_Service) {
			$this->_methods[] = $result;
		} elseif ($result instanceof Mage_Sales_Model_Shipping_Method_Result) {
		    $methods = $result->getAllMethods();
			foreach ($methods as $method) {
			    $this->append($method);
			}
		}
		return $this;
	}
	
	/**
	 * Return all quotes in the result
	 */
	public function getAllMethods()
	{
		return $this->_methods;
	}
	
	/**
	 * Return quotes for specified type
	 *
	 * @param string $type
	 */
	public function getMethodsByVendor($vendor)
	{
		$result = array();
		foreach ($this->_methods as $method) {
			if ($method->vendor===$vendor) {
				$result[] = $method;
			}
		}
		return $result;
	}
}
