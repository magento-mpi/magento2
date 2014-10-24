<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesRule\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class SalesRuleInjectable
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class SalesRuleInjectable extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\SalesRule\Test\Repository\SalesRuleInjectable';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\SalesRule\Test\Handler\SalesRuleInjectable\SalesRuleInjectableInterface';

    protected $defaultDataSet = [
        'name' => 'Default price rule %isolation%',
        'is_active' => 'Active',
        'website_ids' => ['Main Website'],
        'customer_group_ids' => [
            'NOT LOGGED IN',
            'General',
            'Wholesale',
            'Retailer',
        ],
        'coupon_type' => 'No Coupon',
        'simple_action' => 'Percent of product price discount',
        'discount_amount' => '50',
    ];

    protected $rule_id = [
        'attribute_code' => 'rule_id',
        'backend_type' => 'int',
        'is_required' => '1',
    ];

    protected $name = [
        'attribute_code' => 'name',
        'backend_type' => 'varchar',
        'group' => 'rule_information',
    ];

    protected $description = [
        'attribute_code' => 'description',
        'backend_type' => 'text',
        'group' => 'rule_information',
    ];

    protected $from_date = [
        'attribute_code' => 'from_date',
        'backend_type' => 'date',
        'group' => 'rule_information',
        'source' => 'Magento\Backend\Test\Fixture\Date'
    ];

    protected $to_date = [
        'attribute_code' => 'to_date',
        'backend_type' => 'date',
        'group' => 'rule_information',
        'source' => 'Magento\Backend\Test\Fixture\Date'
    ];

    protected $uses_per_customer = [
        'attribute_code' => 'uses_per_customer',
        'backend_type' => 'int',
        'default_value' => '0',
        'group' => 'rule_information',
    ];

    protected $is_active = [
        'attribute_code' => 'is_active',
        'backend_type' => 'smallint',
        'default_value' => '0',
        'group' => 'rule_information',
    ];

    protected $conditions_serialized = [
        'attribute_code' => 'conditions_serialized',
        'backend_type' => 'mediumtext',
        'group' => 'conditions',
        'source' => 'Magento\SalesRule\Test\Fixture\SalesRuleInjectable\ConditionsSerialized'
    ];

    protected $actions_serialized = [
        'attribute_code' => 'actions_serialized',
        'backend_type' => 'mediumtext',
        'group' => 'actions',
    ];

    protected $stop_rules_processing = [
        'attribute_code' => 'stop_rules_processing',
        'backend_type' => 'smallint',
        'default_value' => '1',
        'group' => 'actions',
    ];

    protected $is_advanced = [
        'attribute_code' => 'is_advanced',
        'backend_type' => 'smallint',
        'default_value' => '1',
    ];

    protected $product_ids = [
        'attribute_code' => 'product_ids',
        'backend_type' => 'text',
    ];

    protected $sort_order = [
        'attribute_code' => 'sort_order',
        'backend_type' => 'int',
        'default_value' => '0',
        'group' => 'rule_information',
    ];

    protected $simple_action = [
        'attribute_code' => 'simple_action',
        'backend_type' => 'varchar',
        'group' => 'actions',
    ];

    protected $discount_amount = [
        'attribute_code' => 'discount_amount',
        'backend_type' => 'decimal',
        'default_value' => '0.0000',
        'group' => 'actions',
    ];

    protected $discount_qty = [
        'attribute_code' => 'discount_qty',
        'backend_type' => 'decimal',
        'group' => 'actions',
    ];

    protected $discount_step = [
        'attribute_code' => 'discount_step',
        'backend_type' => 'int',
        'group' => 'actions',
    ];

    protected $apply_to_shipping = [
        'attribute_code' => 'apply_to_shipping',
        'backend_type' => 'smallint',
        'default_value' => '0',
        'group' => 'actions',
    ];

    protected $times_used = [
        'attribute_code' => 'times_used',
        'backend_type' => 'int',
        'default_value' => '0',
    ];

    protected $is_rss = [
        'attribute_code' => 'is_rss',
        'backend_type' => 'smallint',
        'default_value' => '0',
        'group' => 'rule_information',
    ];

    protected $coupon_type = [
        'attribute_code' => 'coupon_type',
        'backend_type' => 'smallint',
        'default_value' => '1',
        'group' => 'rule_information',
    ];

    protected $use_auto_generation = [
        'attribute_code' => 'use_auto_generation',
        'backend_type' => 'smallint',
        'default_value' => '0',
        'group' => 'rule_information',
    ];

    protected $uses_per_coupon = [
        'attribute_code' => 'uses_per_coupon',
        'backend_type' => 'int',
        'default_value' => '0',
        'group' => 'rule_information',
    ];

    protected $simple_free_shipping = [
        'attribute_code' => 'simple_free_shipping',
        'backend_type' => 'smallint',
        'group' => 'actions',
    ];

    protected $id = [
        'attribute_code' => 'id',
        'backend_type' => 'virtual',
    ];

    protected $website_ids = [
        'attribute_code' => 'website_ids',
        'backend_type' => 'virtual',
        'group' => 'rule_information',
    ];

    protected $customer_group_ids = [
        'attribute_code' => 'customer_group_ids',
        'backend_type' => 'virtual',
        'group' => 'rule_information',
    ];

    protected $store_labels = [
        'attribute_code' => 'store_labels',
        'backend_type' => 'virtual',
        'group' => 'labels',
    ];

    protected $coupon_code = [
        'attribute_code' => 'coupon_code',
        'backend_type' => 'virtual',
        'group' => 'rule_information',
    ];

    protected $reward_points_delta = [
        'attribute_code' => 'reward_points_delta',
        'backend_type' => 'virtual',
        'group' => 'actions',
    ];

    protected $rule = [
        'attribute_code' => 'rule',
        'backend_type' => 'virtual',
    ];

    public function getRuleId()
    {
        return $this->getData('rule_id');
    }

    public function getName()
    {
        return $this->getData('name');
    }

    public function getDescription()
    {
        return $this->getData('description');
    }

    public function getFromDate()
    {
        return $this->getData('from_date');
    }

    public function getToDate()
    {
        return $this->getData('to_date');
    }

    public function getUsesPerCustomer()
    {
        return $this->getData('uses_per_customer');
    }

    public function getIsActive()
    {
        return $this->getData('is_active');
    }

    public function getConditionsSerialized()
    {
        return $this->getData('conditions_serialized');
    }

    public function getActionsSerialized()
    {
        return $this->getData('actions_serialized');
    }

    public function getStopRulesProcessing()
    {
        return $this->getData('stop_rules_processing');
    }

    public function getIsAdvanced()
    {
        return $this->getData('is_advanced');
    }

    public function getProductIds()
    {
        return $this->getData('product_ids');
    }

    public function getSortOrder()
    {
        return $this->getData('sort_order');
    }

    public function getSimpleAction()
    {
        return $this->getData('simple_action');
    }

    public function getDiscountAmount()
    {
        return $this->getData('discount_amount');
    }

    public function getDiscountQty()
    {
        return $this->getData('discount_qty');
    }

    public function getDiscountStep()
    {
        return $this->getData('discount_step');
    }

    public function getApplyToShipping()
    {
        return $this->getData('apply_to_shipping');
    }

    public function getTimesUsed()
    {
        return $this->getData('times_used');
    }

    public function getIsRss()
    {
        return $this->getData('is_rss');
    }

    public function getCouponType()
    {
        return $this->getData('coupon_type');
    }

    public function getUseAutoGeneration()
    {
        return $this->getData('use_auto_generation');
    }

    public function getUsesPerCoupon()
    {
        return $this->getData('uses_per_coupon');
    }

    public function getSimpleFreeShipping()
    {
        return $this->getData('simple_free_shipping');
    }

    public function getId()
    {
        return $this->getData('id');
    }

    public function getWebsiteIds()
    {
        return $this->getData('website_ids');
    }

    public function getCustomerGroupIds()
    {
        return $this->getData('customer_group_ids');
    }

    public function getStoreLabels()
    {
        return $this->getData('store_labels');
    }

    public function getCouponCode()
    {
        return $this->getData('coupon_code');
    }

    public function getRewardPointsDelta()
    {
        return $this->getData('reward_points_delta');
    }

    public function getRule()
    {
        return $this->getData('rule');
    }
}
