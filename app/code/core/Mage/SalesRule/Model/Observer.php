<?php

class Mage_SalesRule_Model_Observer
{
	public function onAfterOrder($observer)
	{
		$order = $observer->getEvent()->getOrder();
		
		
	}
}