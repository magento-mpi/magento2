<?php

class Mage_Sales_Model_Quote_Address_Total_Discount
    extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    public function collect(Mage_Sales_Model_Quote_Address $address)
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

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $amount = $address->getDiscountAmount();
        if ($amount!=0) {
            $title = __('Discount');
            if ($address->getQuote()->getCouponCode()) {
                $title .= ' ('.$address->getQuote()->getCouponCode().')';
            }
            $address->addTotal(array(
                'code'=>$this->getCode(), 
                'title'=>$title, 
                'value'=>-$amount
            ));
        }
        return $this;
    }

}