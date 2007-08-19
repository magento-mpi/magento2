<?php

class Mage_Sales_Model_Entity_Quote_Address_Attribute_Backend_Discount
    extends Mage_Sales_Model_Entity_Quote_Address_Attribute_Backend
{
    public function collectTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $validator = Mage::getModel('salesrule/validator')
        	->setCouponCode($address->getQuote()->getCouponCode())
        	->setCustomerGroupId($address->getQuote()->getCustomerGroupId())
        	->setStoreId($address->getQuote()->getStoreId());

        $address->setDiscountAmount(0);

        $appliedRuleIds = '';
        $totalDiscountAmount = 0;
        foreach ($address->getAllItems() as $item) {
        	$validator->process($item);
        	$totalDiscountAmount += $item->getDiscountAmount();
        	$appliedRuleIds = trim($appliedRuleIds.','.$item->getAppliedRuleIds(), ',');
        }

        $address->setCouponCode($validator->getConfirmedCouponCode());
        $address->setDiscountAmount($totalDiscountAmount);
        $address->setAppliedRuleIds($appliedRuleIds);

        $address->setGrandTotal($address->getGrandTotal() - $address->getDiscountAmount());

        return $this;
    }


}