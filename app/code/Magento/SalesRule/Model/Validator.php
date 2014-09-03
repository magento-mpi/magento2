<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Model;

use Magento\Sales\Model\Quote\Item\AbstractItem;
use Magento\Sales\Model\Quote\Address;

/**
 * SalesRule Validator Model
 *
 * Allows dispatching before and after events for each controller action
 *
 * @method mixed getCouponCode()
 * @method \Magento\SalesRule\Model\Validator setCouponCode($code)
 * @method mixed getWebsiteId()
 * @method \Magento\SalesRule\Model\Validator setWebsiteId($id)
 * @method mixed getCustomerGroupId()
 * @method \Magento\SalesRule\Model\Validator setCustomerGroupId($id)
 */
class Validator extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Rule source collection
     *
     * @var \Magento\SalesRule\Model\Resource\Rule\Collection
     */
    protected $_rules;

    /**
     * Defines if method \Magento\SalesRule\Model\Validator::reset() wasn't called
     * Used for clearing applied rule ids in Quote and in Address
     *
     * @var bool
     */
    protected $_isFirstTimeResetRun = true;

    /**
     * Information about item totals for rules
     *
     * @var array
     */
    protected $_rulesItemTotals = array();

    /**
     * Skip action rules validation flag
     *
     * @var bool
     */
    protected $_skipActionsValidation = false;

    /**
     * Catalog data
     *
     * @var \Magento\Catalog\Helper\Data|null
     */
    protected $_catalogData = null;

    /**
     * @var \Magento\SalesRule\Model\Resource\Rule\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\SalesRule\Model\Utility
     */
    protected $validatorUtility;

    /**
     * @var \Magento\SalesRule\Model\RulesApplier
     */
    protected $rulesApplier;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param Resource\Rule\CollectionFactory $collectionFactory
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param Utility $utility
     * @param RulesApplier $rulesApplier
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\SalesRule\Model\Resource\Rule\CollectionFactory $collectionFactory,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\SalesRule\Model\Utility $utility,
        \Magento\SalesRule\Model\RulesApplier $rulesApplier,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_catalogData = $catalogData;
        $this->validatorUtility = $utility;
        $this->rulesApplier = $rulesApplier;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Init validator
     * Init process load collection of rules for specific website,
     * customer group and coupon code
     *
     * @param int $websiteId
     * @param int $customerGroupId
     * @param string $couponCode
     * @return $this
     */
    public function init($websiteId, $customerGroupId, $couponCode)
    {
        $this->setWebsiteId($websiteId)->setCustomerGroupId($customerGroupId)->setCouponCode($couponCode);

        $key = $websiteId . '_' . $customerGroupId . '_' . $couponCode;
        if (!isset($this->_rules[$key])) {
            $this->_rules[$key] = $this->_collectionFactory->create()
                ->setValidationFilter(
                    $websiteId,
                    $customerGroupId,
                    $couponCode
                )
                ->addFieldToFilter('is_active', 1)
                ->load();
        }
        return $this;
    }

    /**
     * Get rules collection for current object state
     *
     * @return \Magento\SalesRule\Model\Resource\Rule\Collection
     */
    protected function _getRules()
    {
        $key = $this->getWebsiteId() . '_' . $this->getCustomerGroupId() . '_' . $this->getCouponCode();
        return $this->_rules[$key];
    }

    /**
     * Set skip actions validation flag
     *
     * @param bool $flag
     * @return $this
     */
    public function setSkipActionsValidation($flag)
    {
        $this->_skipActionsValidation = $flag;
        return $this;
    }

    /**
     * Can apply rules check
     *
     * @param AbstractItem $item
     * @return bool
     */
    public function canApplyRules(AbstractItem $item)
    {
        $address = $item->getAddress();
        foreach ($this->_getRules() as $rule) {
            if (!$this->validatorUtility->canProcessRule($rule, $address) || !$rule->getActions()->validate($item)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Reset quote and address applied rules
     *
     * @param Address $address
     * @return $this
     */
    public function reset(Address $address)
    {
        $this->validatorUtility->resetRoundingDeltas();
        if ($this->_isFirstTimeResetRun) {
            $address->setAppliedRuleIds('');
            $address->getQuote()->setAppliedRuleIds('');
            $this->_isFirstTimeResetRun = false;
        }

        return $this;
    }

    /**
     * Quote item discount calculation process
     *
     * @param AbstractItem $item
     * @return $this
     */
    public function process(AbstractItem $item)
    {
        $item->setDiscountAmount(0);
        $item->setBaseDiscountAmount(0);
        $item->setDiscountPercent(0);

        $itemPrice = $this->getItemPrice($item);
        if ($itemPrice < 0) {
            return $this;
        }

        $appliedRuleIds = $this->rulesApplier->applyRules(
            $item,
            $this->_getRules(),
            $this->_skipActionsValidation,
            $this->getCouponCode()
        );
        $this->rulesApplier->setAppliedRuleIds($item, $appliedRuleIds);

        return $this;
    }

    /**
     * Apply discounts to shipping amount
     *
     * @param Address $address
     * @return $this
     */
    public function processShippingAmount(Address $address)
    {
        $shippingAmount = $address->getShippingAmountForDiscount();
        if ($shippingAmount !== null) {
            $baseShippingAmount = $address->getBaseShippingAmountForDiscount();
        } else {
            $shippingAmount = $address->getShippingAmount();
            $baseShippingAmount = $address->getBaseShippingAmount();
        }
        $quote = $address->getQuote();
        $appliedRuleIds = array();
        foreach ($this->_getRules() as $rule) {
            /* @var \Magento\SalesRule\Model\Rule $rule */
            if (!$rule->getApplyToShipping() || !$this->validatorUtility->canProcessRule($rule, $address)) {
                continue;
            }

            $discountAmount = 0;
            $baseDiscountAmount = 0;
            $rulePercent = min(100, $rule->getDiscountAmount());
            switch ($rule->getSimpleAction()) {
                case \Magento\SalesRule\Model\Rule::TO_PERCENT_ACTION:
                    $rulePercent = max(0, 100 - $rule->getDiscountAmount());
                // break is intentionally omitted
                case \Magento\SalesRule\Model\Rule::BY_PERCENT_ACTION:
                    $discountAmount = ($shippingAmount - $address->getShippingDiscountAmount()) * $rulePercent / 100;
                    $baseDiscountAmount = ($baseShippingAmount -
                            $address->getBaseShippingDiscountAmount()) * $rulePercent / 100;
                    $discountPercent = min(100, $address->getShippingDiscountPercent() + $rulePercent);
                    $address->setShippingDiscountPercent($discountPercent);
                    break;
                case \Magento\SalesRule\Model\Rule::TO_FIXED_ACTION:
                    $quoteAmount = $quote->getStore()->convertPrice($rule->getDiscountAmount());
                    $discountAmount = $shippingAmount - $quoteAmount;
                    $baseDiscountAmount = $baseShippingAmount - $rule->getDiscountAmount();
                    break;
                case \Magento\SalesRule\Model\Rule::BY_FIXED_ACTION:
                    $quoteAmount = $quote->getStore()->convertPrice($rule->getDiscountAmount());
                    $discountAmount = $quoteAmount;
                    $baseDiscountAmount = $rule->getDiscountAmount();
                    break;
                case \Magento\SalesRule\Model\Rule::CART_FIXED_ACTION:
                    $cartRules = $address->getCartFixedRules();
                    if (!isset($cartRules[$rule->getId()])) {
                        $cartRules[$rule->getId()] = $rule->getDiscountAmount();
                    }
                    if ($cartRules[$rule->getId()] > 0) {
                        $quoteAmount = $quote->getStore()->convertPrice($cartRules[$rule->getId()]);
                        $discountAmount = min($shippingAmount - $address->getShippingDiscountAmount(), $quoteAmount);
                        $baseDiscountAmount = min(
                            $baseShippingAmount - $address->getBaseShippingDiscountAmount(),
                            $cartRules[$rule->getId()]
                        );
                        $cartRules[$rule->getId()] -= $baseDiscountAmount;
                    }

                    $address->setCartFixedRules($cartRules);
                    break;
            }

            $discountAmount = min($address->getShippingDiscountAmount() + $discountAmount, $shippingAmount);
            $baseDiscountAmount = min(
                $address->getBaseShippingDiscountAmount() + $baseDiscountAmount,
                $baseShippingAmount
            );
            $address->setShippingDiscountAmount($discountAmount);
            $address->setBaseShippingDiscountAmount($baseDiscountAmount);
            $appliedRuleIds[$rule->getRuleId()] = $rule->getRuleId();

            $this->rulesApplier->maintainAddressCouponCode($address, $rule, $this->getCouponCode());
            $this->rulesApplier->addDiscountDescription($address, $rule);
            if ($rule->getStopRulesProcessing()) {
                break;
            }
        }

        $address->setAppliedRuleIds($this->validatorUtility->mergeIds($address->getAppliedRuleIds(), $appliedRuleIds));
        $quote->setAppliedRuleIds($this->validatorUtility->mergeIds($quote->getAppliedRuleIds(), $appliedRuleIds));

        return $this;
    }

    /**
     * Calculate quote totals for each rule and save results
     *
     * @param mixed $items
     * @param Address $address
     * @return $this
     */
    public function initTotals($items, Address $address)
    {
        $address->setCartFixedRules(array());

        if (!$items) {
            return $this;
        }

        foreach ($this->_getRules() as $rule) {
            if (\Magento\SalesRule\Model\Rule::CART_FIXED_ACTION == $rule->getSimpleAction()
                && $this->validatorUtility->canProcessRule($rule, $address)
            ) {
                $ruleTotalItemsPrice = 0;
                $ruleTotalBaseItemsPrice = 0;
                $validItemsCount = 0;

                foreach ($items as $item) {
                    //Skipping child items to avoid double calculations
                    if ($item->getParentItemId()) {
                        continue;
                    }
                    if (!$rule->getActions()->validate($item)) {
                        continue;
                    }
                    $qty = $this->validatorUtility->getItemQty($item, $rule);
                    $ruleTotalItemsPrice += $this->getItemPrice($item) * $qty;
                    $ruleTotalBaseItemsPrice += $this->getItemBasePrice($item) * $qty;
                    $validItemsCount++;
                }

                $this->_rulesItemTotals[$rule->getId()] = array(
                    'items_price' => $ruleTotalItemsPrice,
                    'base_items_price' => $ruleTotalBaseItemsPrice,
                    'items_count' => $validItemsCount
                );
            }
        }

        return $this;
    }

    /**
     * Return item price
     *
     * @param AbstractItem $item
     * @return float
     */
    public function getItemPrice($item)
    {
        $price = $item->getDiscountCalculationPrice();
        $calcPrice = $item->getCalculationPrice();
        return $price === null ? $calcPrice : $price;
    }

    /**
     * Return item original price
     *
     * @param AbstractItem $item
     * @return float
     */
    public function getItemOriginalPrice($item)
    {
        return $this->_catalogData->getTaxPrice($item, $item->getOriginalPrice(), true);
    }

    /**
     * Return item base price
     *
     * @param AbstractItem $item
     * @return float
     */
    public function getItemBasePrice($item)
    {
        $price = $item->getDiscountCalculationPrice();
        return $price !== null ? $item->getBaseDiscountCalculationPrice() : $item->getBaseCalculationPrice();
    }

    /**
     * Return item base original price
     *
     * @param AbstractItem $item
     * @return float
     */
    public function getItemBaseOriginalPrice($item)
    {
        return $this->_catalogData->getTaxPrice($item, $item->getBaseOriginalPrice(), true);
    }

    /**
     * Convert address discount description array to string
     *
     * @param Address $address
     * @param string $separator
     * @return $this
     */
    public function prepareDescription($address, $separator = ', ')
    {
        $descriptionArray = $address->getDiscountDescriptionArray();
        if (!$descriptionArray && $address->getQuote()->getItemVirtualQty() > 0) {
            $descriptionArray = $address->getQuote()->getBillingAddress()->getDiscountDescriptionArray();
        }

        $description = $descriptionArray && is_array(
            $descriptionArray
        ) ? implode(
            $separator,
            array_unique($descriptionArray)
        ) : '';

        $address->setDiscountDescription($description);
        return $this;
    }

    /**
     * Return items list sorted by possibility to apply prioritized rules
     *
     * @param array $items
     * @return array $items
     */
    public function sortItemsByPriority($items)
    {
        $itemsSorted = array();
        /** @var $rule \Magento\SalesRule\Model\Rule */
        foreach ($this->_getRules() as $rule) {
            foreach ($items as $itemKey => $itemValue) {
                if ($rule->getActions()->validate($itemValue)) {
                    unset($items[$itemKey]);
                    array_push($itemsSorted, $itemValue);
                }
            }
        }

        if (!empty($itemsSorted)) {
            $items = array_merge($itemsSorted, $items);
        }

        return $items;
    }

    /**
     * @param int $key
     * @return array
     * @throws \Magento\Framework\Model\Exception
     */
    public function getRuleItemTotalsInfo($key)
    {
        if (empty($this->_rulesItemTotals[$key])) {
            throw new \Magento\Framework\Model\Exception(__('Item totals are not set for the rule.'));
        }

        return $this->_rulesItemTotals[$key];
    }

    /**
     * @param int $key
     * @return $this
     */
    public function decrementRuleItemTotalsCount($key)
    {
        $this->_rulesItemTotals[$key]['items_count']--;

        return $this;
    }
}
