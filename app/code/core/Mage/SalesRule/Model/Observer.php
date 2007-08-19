<?php

class Mage_SalesRule_Model_Observer
{
	public function sales_order_afterSave($observer)
	{
		$order = $observer->getEvent()->getOrder();
		
		$customerId = $order->getCustomerId();
		$ruleIds = explode(',', $order->getAppliedRuleIds());
		
		$ruleCustomer = Mage::getModel('salesrule/rule_customer');
		foreach ($ruleIds as $ruleId) {
			if (!$ruleId) {
				continue;
			}
			$ruleCustomer->loadByCustomerRule($customerId, $ruleId);
			if ($ruleCustomer->getId()) {
				$ruleCustomer->setTimesUsed($ruleCustomer->getTimesUsed()+1);
			} else {
				$ruleCustomer
					->setCustomerId($customerId)
					->setRuleId($ruleId)
					->setTimesUsed(1);
			}
			echo "<pre>".print_r($ruleCustomer->getData(),1)."</pre>";
			$ruleCustomer->save();
		}
	}
}