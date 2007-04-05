<?php

class Mage_Sales_Shipping_Quote_Result
{
	protected $_quotes = array();
	
	/**
	 * Reset result
	 */
	public function reset()
	{
	    $this->_quotes = array();
	    return $this;
	}
	
	/**
	 * Add a quote to the result
	 *
	 * @param Mage_Sales_Shipping_Quote_Service|Mage_Sales_Shipping_Quote_Result $result
	 */
	public function append($result)
	{
		if ($result instanceof Mage_Sales_Shipping_Quote_Service) {
			$this->_quotes[] = $result;
		} elseif ($result instanceof Mage_Sales_Shipping_Quote_Result) {
		    $quotes = $result->getAllQuotes();
			foreach ($quotes as $quote) {
			    $this->append($quote);
			}
		}
		return $this;
	}
	
	/**
	 * Return all quotes in the result
	 */
	public function getAllQuotes()
	{
		return $this->_quotes;
	}
	
	/**
	 * Return quotes for specified type
	 *
	 * @param string $type
	 */
	public function getQuotesByType($type)
	{
		$result = array();
		foreach ($this->_quotes as $quote) {
			if ($quote->type===$type) {
				$result[] = $type;
			}
		}
		return $result;
	}
}
