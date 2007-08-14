<?php

class Mage_Shipping_Model_Rate_Result
{
	protected $_rates = array();
	protected $_error = null;
	
	/**
	 * Reset result
	 */
	public function reset()
	{
	    $this->_rates = array();
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
	 * Add a rate to the result
	 *
	 * @param Mage_Shipping_Model_Rate_Result_Abstract|Mage_Shipping_Model_Rate_Result $result
	 */
	public function append($result)
	{
		if ($result instanceof Mage_Shipping_Model_Rate_Result_Abstract) {
			$this->_rates[] = $result;
		} elseif ($result instanceof Mage_Shipping_Model_Rate_Result) {
		    $rates = $result->getAllRates();
			foreach ($rates as $rate) {
			    $this->append($rate);
			}
		}
		return $this;
	}
	
	/**
	 * Return all quotes in the result
	 */
	public function getAllRates()
	{
		return $this->_rates;
	}
	
	/**
	 * Return quotes for specified type
	 *
	 * @param string $type
	 */
	public function getRatesByCarrier($carrier)
	{
		$result = array();
		foreach ($this->_rates as $rate) {
			if ($rate->getCarrier()===$carrier) {
				$result[] = $rate;
			}
		}
		return $result;
	}
	
	public function asArray()
	{
        $currencyFilter = Mage::getSingleton('core/store')->getPriceFilter();
        $rates = array();
        $allRates = $this->getAllRates();
        foreach ($allRates as $rate) {
            $rates[$rate->getCarrier()]['title'] = $rate->getCarrierTitle();
            $rates[$rate->getCarrier()]['methods'][$rate->getMethod()] = array(
                'title'=>$rate->getMethodTitle(),
                'price'=>$rate->getPrice(),
                'price_formatted'=>$currencyFilter->filter($rate->getPrice()),
            );
        }
        return $rates;
	}
	
	public function getCheapestRate()
	{
	    $cheapest = null;
	    $minPrice = 100000;
	    foreach ($this->getAllRates() as $rate) {
	        if (is_numeric($rate->getPrice()) && $rate->getPrice()<$minPrice) {
	            $cheapest = $rate;
	            $minPrice = $rate->getPrice();
	        }
	    }
	    return $cheapest;
	}
}
