<?php

class Mage_SalesRule_Model_Validator extends Mage_Core_Model_Abstract
{
	protected function _construct()
	{
        parent::_construct();
		$this->_init('salesrule/validator');
		$this->setIsCouponCodeConfirmed(false);
	}

	public function getConfirmedCouponCode()
	{
		if ($this->getIsCouponCodeConfirmed()) {
			return $this->getCouponCode();
		}
		return false;
	}

	public function process(Mage_Core_Model_Abstract $item) {
		if (!$item instanceof Mage_Sales_Model_Quote_Item
			&& !$item instanceof Mage_Sales_Model_Quote_Address_Item) {
			throw Mage::exception('Mage_SalesRule', 'Invalid item entity');
		}

		$item->setDiscountAmount(0);
		$item->setDiscountPercent(0);
		
		$quote = $item->getQuote();
		
		$rule = Mage::getModel('salesrule/rule');
		
		$appliedRuleIds = array();

		$actions = $this->getActionsCollection($item);
		foreach ($actions as $a) {
			if (!$rule->load($a->getRuleId())->validateQuote()) {
				continue;
			}
			
			switch ($a->getActionOperator()) {
				case 'by_percent':
					$item->setDiscountPercent($item->getDiscountPercent()+$a->getActionValue());
					break;

				case 'by_fixed':
					$item->setDiscountAmount($item->getDiscountAmount()+$a->getActionValue());
					break;
			}
			
			$appliedRuleIds[$a->getRuleId()] = true;
			
			if ($a->getActionStop()) {
				break;
			}
		}
		
		$discountAmount = $item->getDiscountAmount() + $item->getRowTotal()*$item->getDiscountPercent()/100;
		$discountAmount = max($discountAmount, $item->getRowTotal());
		$item->setDiscountAmount($discountAmount);
		
		$item->setAppliedRuleIds(join(',',$appliedRuleIds));
		
		return $this;
	}

	public function getActionsCollection($item)
	{
		$actions = Mage::getResourceModel('salesrule/rule_product_collection')
			->addFieldToFilter('coupon_code', array(array('null'=>true), $this->getCouponCode()))
			->addFieldToFilter('from_time', array('lteq'=>time()))
			->addFieldToFilter('to_time', array('gteq'=>time()))
			->addFieldToFilter('customer_group_id', $this->getCustomerGroupId())
			->addFieldToFilter('store_id', $this->getStoreId())
			->addFieldToFilter('product_id', $item->getProductId())
			->setOrder('sort_order')
			->load();
		return $actions;
	}
}