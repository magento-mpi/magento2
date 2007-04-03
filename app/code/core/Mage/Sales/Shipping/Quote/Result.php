<?php

class Mage_Sales_Shipping_Quote_Result
{
	protected $_quotes = array();
	
	/**
	 * Add a quote to the result
	 *
	 * @param Mage_Sales_Shipping_Quote|Mage_Sales_Shipping_Quote_Result $result
	 */
	function append($result)
	{
		if ($result instanceof Mage_Sales_Shipping_Quote) {
			$this->_quotes[] = $result;
		} elseif ($result instanceof Mage_Sales_Shipping_Quote_Result) {
			$quotes = $result->getAllQuotes();
			foreach ($quotes as $quote) {
				$this->append($quote);
			}
		}
	}
	
	/**
	 * Return all quotes in the result
	 */
	function getAllQuotes()
	{
		return $this->_quotes;
	}
	
	/**
	 * Return quotes for specified type
	 *
	 * @param string $type
	 */
	function getQuotesByType($type)
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

?>