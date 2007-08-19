<?php

class Mage_GoogleAnalytics_Model_Observer
{
	public function order_success_page_view($observer)
	{
		$quoteId = Mage::getSingleton('checkout/session')->getLastQuoteId();
		if ($quoteId) {
			$quote = Mage::getModel('sales/quote')
				->load($quoteId);
			
			Mage::registry('action')->getLayout()->getBlock('urchin')
				->setQuote($quote);
		}
	}
}