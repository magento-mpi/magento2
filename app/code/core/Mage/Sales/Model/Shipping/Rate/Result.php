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
	
	public function asArray()
	{
        $currencyFilter = new Varien_Filter_Sprintf('$%s', 2);
        $methods = array();
        $allMethods = $this->getAllMethods();
        foreach ($allMethods as $method) {
            $methods[$method->getVendor()]['title'] = $method->getVendorTitle();
            $methods[$method->getVendor()]['methods'][$method->getService()] = array(
                'title'=>$method->getServiceTitle(),
                'price'=>$method->getPrice(),
                'price_formatted'=>$currencyFilter->filter($method->getPrice()),
            );
        }
        return $methods;
	}
	
	public function getCheapestMethod()
	{
	    $cheapest = null;
	    $minPrice = 100000;
	    foreach ($this->getAllMethods() as $method) {
	        if (is_numeric($method->getPrice()) && $method->getPrice()<$minPrice) {
	            $cheapest = $method;
	            $minPrice = $method->getPrice();
	        }
	    }
	    return $cheapest;
	}
}
