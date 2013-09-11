<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\SalesRule\Model;

class Observer
{
    protected $_validator;

    public function salesOrderAfterPlace($observer)
    {
        $order = $observer->getEvent()->getOrder();

        if (!$order) {
            return $this;
        }

        // lookup rule ids
        $ruleIds = explode(',', $order->getAppliedRuleIds());
        $ruleIds = array_unique($ruleIds);

        $ruleCustomer = null;
        $customerId = $order->getCustomerId();

        // use each rule (and apply to customer, if applicable)
        foreach ($ruleIds as $ruleId) {
            if (!$ruleId) {
                continue;
            }
            $rule = \Mage::getModel('\Magento\SalesRule\Model\Rule');
            $rule->load($ruleId);
            if ($rule->getId()) {
                $rule->setTimesUsed($rule->getTimesUsed() + 1);
                $rule->save();

                if ($customerId) {
                    $ruleCustomer = \Mage::getModel('\Magento\SalesRule\Model\Rule\Customer');
                    $ruleCustomer->loadByCustomerRule($customerId, $ruleId);

                    if ($ruleCustomer->getId()) {
                        $ruleCustomer->setTimesUsed($ruleCustomer->getTimesUsed()+1);
                    }
                    else {
                        $ruleCustomer
                        ->setCustomerId($customerId)
                        ->setRuleId($ruleId)
                        ->setTimesUsed(1);
                    }
                    $ruleCustomer->save();
                }
            }
        }

        $coupon = \Mage::getModel('\Magento\SalesRule\Model\Coupon');
        /** @var \Magento\SalesRule\Model\Coupon */
        $coupon->load($order->getCouponCode(), 'code');
        if ($coupon->getId()) {
            $coupon->setTimesUsed($coupon->getTimesUsed() + 1);
            $coupon->save();
            if ($customerId) {
                $couponUsage = \Mage::getResourceModel('\Magento\SalesRule\Model\Resource\Coupon\Usage');
                $couponUsage->updateCustomerCouponTimesUsed($customerId, $coupon->getId());
            }
        }
    }

    /**
     * Refresh sales coupons report statistics for last day
     *
     * @param \Magento\Cron\Model\Schedule $schedule
     * @return \Magento\SalesRule\Model\Observer
     */
    public function aggregateSalesReportCouponsData($schedule)
    {
        \Mage::app()->getLocale()->emulate(0);
        $currentDate = \Mage::app()->getLocale()->date();
        $date = $currentDate->subHour(25);
        \Mage::getResourceModel('\Magento\SalesRule\Model\Resource\Report\Rule')->aggregate($date);
        \Mage::app()->getLocale()->revert();
        return $this;
    }

    /**
     * Check rules that contains affected attribute
     * If rules were found they will be set to inactive and notice will be add to admin session
     *
     * @param string $attributeCode
     * @return \Magento\SalesRule\Model\Observer
     */
    protected function _checkSalesRulesAvailability($attributeCode)
    {
        /* @var $collection \Magento\SalesRule\Model\Resource\Rule\Collection */
        $collection = \Mage::getResourceModel('\Magento\SalesRule\Model\Resource\Rule\Collection')
            ->addAttributeInConditionFilter($attributeCode);

        $disabledRulesCount = 0;
        foreach ($collection as $rule) {
            /* @var $rule \Magento\SalesRule\Model\Rule */
            $rule->setIsActive(0);
            /* @var $rule->getConditions() \Magento\SalesRule\Model\Rule\Condition\Combine */
            $this->_removeAttributeFromConditions($rule->getConditions(), $attributeCode);
            $this->_removeAttributeFromConditions($rule->getActions(), $attributeCode);
            $rule->save();

            $disabledRulesCount++;
        }

        if ($disabledRulesCount) {
            \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addWarning(
                __('%1 Shopping Cart Price Rules based on "%2" attribute have been disabled.', $disabledRulesCount, $attributeCode));
        }

        return $this;
    }

    /**
     * Remove catalog attribute condition by attribute code from rule conditions
     *
     * @param \Magento\Rule\Model\Condition\Combine $combine
     * @param string $attributeCode
     */
    protected function _removeAttributeFromConditions($combine, $attributeCode)
    {
        $conditions = $combine->getConditions();
        foreach ($conditions as $conditionId => $condition) {
            if ($condition instanceof \Magento\Rule\Model\Condition\Combine) {
                $this->_removeAttributeFromConditions($condition, $attributeCode);
            }
            if ($condition instanceof \Magento\SalesRule\Model\Rule\Condition\Product) {
                if ($condition->getAttribute() == $attributeCode) {
                    unset($conditions[$conditionId]);
                }
            }
        }
        $combine->setConditions($conditions);
    }

    /**
     * After save attribute if it is not used for promo rules already check rules for containing this attribute
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\SalesRule\Model\Observer
     */
    public function catalogAttributeSaveAfter(\Magento\Event\Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute->dataHasChangedFor('is_used_for_promo_rules') && !$attribute->getIsUsedForPromoRules()) {
            $this->_checkSalesRulesAvailability($attribute->getAttributeCode());
        }

        return $this;
    }

    /**
     * After delete attribute check rules that contains deleted attribute
     * If rules was found they will seted to inactive and added notice to admin session
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\SalesRule\Model\Observer
     */
    public function catalogAttributeDeleteAfter(\Magento\Event\Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute->getIsUsedForPromoRules()) {
            $this->_checkSalesRulesAvailability($attribute->getAttributeCode());
        }

        return $this;
    }

    /**
     * Append sales rule product attributes to select by quote item collection
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\SalesRule\Model\Observer
     */
    public function addProductAttributes(\Magento\Event\Observer $observer)
    {
        // @var \Magento\Object
        $attributesTransfer = $observer->getEvent()->getAttributes();

        $attributes = \Mage::getResourceModel('\Magento\SalesRule\Model\Resource\Rule')
            ->getActiveAttributes(
                \Mage::app()->getWebsite()->getId(),
                \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomer()->getGroupId()
            );
        $result = array();
        foreach ($attributes as $attribute) {
            $result[$attribute['attribute_code']] = true;
        }
        $attributesTransfer->addData($result);
        return $this;
    }

    /**
     * Add coupon's rule name to order data
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\SalesRule\Model\Observer
     */
    public function addSalesRuleNameToOrder($observer)
    {
        $order = $observer->getOrder();
        $couponCode = $order->getCouponCode();

        if (empty($couponCode)) {
            return $this;
        }

        /**
         * @var \Magento\SalesRule\Model\Coupon $couponModel
         */
        $couponModel = \Mage::getModel('\Magento\SalesRule\Model\Coupon');
        $couponModel->loadByCode($couponCode);

        $ruleId = $couponModel->getRuleId();

        if (empty($ruleId)) {
            return $this;
        }

        /**
         * @var \Magento\SalesRule\Model\Rule $ruleModel
         */
        $ruleModel = \Mage::getModel('\Magento\SalesRule\Model\Rule');
        $ruleModel->load($ruleId);

        $order->setCouponRuleName($ruleModel->getName());

        return $this;
    }
}

