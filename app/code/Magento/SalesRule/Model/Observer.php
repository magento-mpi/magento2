<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_SalesRule_Model_Observer
{
    /**
     * @var Magento_Backend_Model_Session
     */
    protected $_backendSession;

    /**
     * @var Magento_Backend_Model_Session
     */
    protected $_usageFactory;

    /**
     * @var Magento_SalesRule_Model_Resource_Coupon_UsageFactory
     */
    protected $_ruleFactory;

    /**
     * @var Magento_SalesRule_Model_Resource_Rule_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param Magento_SalesRule_Model_Resource_Rule_CollectionFactory $collectionFactory
     * @param Magento_SalesRule_Model_Resource_Report_RuleFactory $ruleFactory
     * @param Magento_SalesRule_Model_Resource_Coupon_UsageFactory $usageFactory
     * @param Magento_Backend_Model_Session $backendSession
     */
    function __construct(
        Magento_SalesRule_Model_Resource_Rule_CollectionFactory $collectionFactory,
        Magento_SalesRule_Model_Resource_Report_RuleFactory $ruleFactory,
        Magento_SalesRule_Model_Resource_Coupon_UsageFactory $usageFactory,
        Magento_Backend_Model_Session $backendSession
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_ruleFactory = $ruleFactory;
        $this->_usageFactory = $usageFactory;
        $this->_backendSession = $backendSession;
    }

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
            $rule = Mage::getModel('Magento_SalesRule_Model_Rule');
            $rule->load($ruleId);
            if ($rule->getId()) {
                $rule->setTimesUsed($rule->getTimesUsed() + 1);
                $rule->save();

                if ($customerId) {
                    $ruleCustomer = Mage::getModel('Magento_SalesRule_Model_Rule_Customer');
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

        $coupon = Mage::getModel('Magento_SalesRule_Model_Coupon');
        /** @var Magento_SalesRule_Model_Coupon */
        $coupon->load($order->getCouponCode(), 'code');
        if ($coupon->getId()) {
            $coupon->setTimesUsed($coupon->getTimesUsed() + 1);
            $coupon->save();
            if ($customerId) {
                $this->_usageFactory->create()->updateCustomerCouponTimesUsed($customerId, $coupon->getId());
            }
        }
    }

    /**
     * Refresh sales coupons report statistics for last day
     *
     * @param Magento_Cron_Model_Schedule $schedule
     * @return Magento_SalesRule_Model_Observer
     */
    public function aggregateSalesReportCouponsData($schedule)
    {
        Mage::app()->getLocale()->emulate(0);
        $currentDate = Mage::app()->getLocale()->date();
        $date = $currentDate->subHour(25);
        $this->_ruleFactory->create()->aggregate($date);
        Mage::app()->getLocale()->revert();
        return $this;
    }

    /**
     * Check rules that contains affected attribute
     * If rules were found they will be set to inactive and notice will be add to admin session
     *
     * @param string $attributeCode
     * @return Magento_SalesRule_Model_Observer
     */
    protected function _checkSalesRulesAvailability($attributeCode)
    {
        /* @var $collection Magento_SalesRule_Model_Resource_Rule_Collection */
        $collection = $this->_collectionFactory->create()->addAttributeInConditionFilter($attributeCode);

        $disabledRulesCount = 0;
        foreach ($collection as $rule) {
            /* @var $rule Magento_SalesRule_Model_Rule */
            $rule->setIsActive(0);
            /* @var $rule->getConditions() Magento_SalesRule_Model_Rule_Condition_Combine */
            $this->_removeAttributeFromConditions($rule->getConditions(), $attributeCode);
            $this->_removeAttributeFromConditions($rule->getActions(), $attributeCode);
            $rule->save();

            $disabledRulesCount++;
        }

        if ($disabledRulesCount) {
            $this->_backendSession->addWarning(
                __('%1 Shopping Cart Price Rules based on "%2" attribute have been disabled.', $disabledRulesCount, $attributeCode));
        }

        return $this;
    }

    /**
     * Remove catalog attribute condition by attribute code from rule conditions
     *
     * @param Magento_Rule_Model_Condition_Combine $combine
     * @param string $attributeCode
     */
    protected function _removeAttributeFromConditions($combine, $attributeCode)
    {
        $conditions = $combine->getConditions();
        foreach ($conditions as $conditionId => $condition) {
            if ($condition instanceof Magento_Rule_Model_Condition_Combine) {
                $this->_removeAttributeFromConditions($condition, $attributeCode);
            }
            if ($condition instanceof Magento_SalesRule_Model_Rule_Condition_Product) {
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
     * @param Magento_Event_Observer $observer
     * @return Magento_SalesRule_Model_Observer
     */
    public function catalogAttributeSaveAfter(Magento_Event_Observer $observer)
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
     * @param Magento_Event_Observer $observer
     * @return Magento_SalesRule_Model_Observer
     */
    public function catalogAttributeDeleteAfter(Magento_Event_Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute->getIsUsedForPromoRules()) {
            $this->_checkSalesRulesAvailability($attribute->getAttributeCode());
        }

        return $this;
    }

    /**
     * Add coupon's rule name to order data
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_SalesRule_Model_Observer
     */
    public function addSalesRuleNameToOrder($observer)
    {
        $order = $observer->getOrder();
        $couponCode = $order->getCouponCode();

        if (empty($couponCode)) {
            return $this;
        }

        /**
         * @var Magento_SalesRule_Model_Coupon $couponModel
         */
        $couponModel = Mage::getModel('Magento_SalesRule_Model_Coupon');
        $couponModel->loadByCode($couponCode);

        $ruleId = $couponModel->getRuleId();

        if (empty($ruleId)) {
            return $this;
        }

        /**
         * @var Magento_SalesRule_Model_Rule $ruleModel
         */
        $ruleModel = Mage::getModel('Magento_SalesRule_Model_Rule');
        $ruleModel->load($ruleId);

        $order->setCouponRuleName($ruleModel->getName());

        return $this;
    }
}

