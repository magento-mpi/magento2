<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesRule\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class SalesRuleInjectable
 * Data for creation Sales Rule
 */
class SalesRuleInjectable extends AbstractRepository
{
    /**
     * @constructor
     * @param array $defaultConfig
     * @param array $defaultData
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['active_sales_rule_with_percent_price_discount_coupon'] = [
            'name' => 'Shopping Cart Price Rule with Specific Coupon %isolation%',
            'description' => 'Description for Cart Price Rule',
            'is_active' => 'Active',
            'website_ids' => ['Main Website'],
            'customer_group_ids' => ['NOT LOGGED IN'],
            'coupon_type' => 'Specific Coupon',
            'coupon_code' => '123-abc-ABC-987-%isolation%',
            'simple_action' => 'Percent of product price discount',
            'discount_amount' => '50',
            'store_labels' => [
                0 => 'Shopping Cart price Rule with Specific Coupon',
                1 => 'Shopping Cart price Rule with Specific Coupon'
            ],
        ];

        $this->_data['active_sales_rule_with_coupon_10'] = [
            'name' => '10% Off Coupon',
            'is_active' => 'Active',
            'website_ids' => ['Main Website'],
            'customer_group_ids' => ['NOT LOGGED IN'],
            'coupon_type' => 'Specific Coupon',
            'coupon_code' => '1234',
            'simple_action' => 'Percent of product price discount',
            'discount_amount' => '10',
        ];

        $this->_data['active_sales_rule_with_fixed_price_discount_coupon'] = [
            'name' => 'Shopping Cart Price Rule with Specific Coupon %isolation%',
            'description' => 'Description for Cart Price Rule',
            'is_active' => 'Active',
            'website_ids' => ['Main Website'],
            'customer_group_ids' => ['NOT LOGGED IN', 'General'],
            'coupon_type' => 'Specific Coupon',
            'coupon_code' => '123-abc-ABC-987-%isolation%',
            'simple_action' => 'Fixed amount discount',
            'discount_amount' => '50',
            'store_labels' => [
                0 => 'Shopping Cart price Rule with Specific Coupon',
                1 => 'Shopping Cart price Rule with Specific Coupon'
            ],
        ];

        $this->_data['active_sales_rule_for_retailer'] = [
            'name' => 'Shopping Cart Price Rule %isolation%',
            'description' => 'Description for Cart Price Rule',
            'is_active' => 'Active',
            'website_ids' => ['Main Website'],
            'customer_group_ids' => ['Retailer'],
            'coupon_type' => 'No Coupon',
            'simple_action' => 'Percent of product price discount',
            'discount_amount' => '50',
            'stop_rules_processing' => 'Yes',
        ];

        $this->_data['active_sales_rule_with_complex_conditions'] = [
            'name' => 'Shopping Cart Price Rule with with complex conditions %isolation%',
            'description' => 'Shopping Cart Price Rule with with complex conditions',
            'is_active' => 'Active',
            'website_ids' => ['Main Website'],
            'customer_group_ids' => ['NOT LOGGED IN', 'General', 'Wholesale', 'Retailer'],
            'coupon_type' => 'Specific Coupon',
            'coupon_code' => '123-abc-ABC-987-%isolation%',
            'uses_per_coupon' => '13',
            'uses_per_customer' => '63',
            'from_date' => ['pattern' => '3/25/2014'],
            'to_date' => ['pattern' => '6/29/2024'],
            'sort_order' => '1',
            'is_rss' => 'Yes',
            'conditions_serialized' => '[Subtotal|is|300]{Conditions combination:'
                . '[[Shipping Country|is|United States][Shipping Postcode|is|123456789a]]}',
            'actions_serialized' => '[Category|is|2]',
            'simple_action' => 'Percent of product price discount',
            'discount_amount' => '50',
            'discount_step' => '0',
            'apply_to_shipping' => 'Yes',
            'stop_rules_processing' => 'Yes',
            'reward_points_delta' => '500',
            'simple_free_shipping' => 'For matching items only',
            'store_labels' => [
                0 => 'Shopping Cart Price Rule with with complex conditions',
                1 => 'Shopping Cart Price Rule with with complex conditions',
            ],
        ];

        $this->_data['inactive_sales_rule'] = [
            'name' => 'Inactive Cart Price Rule %isolation%',
            'is_active' => 'Inactive',
            'website_ids' => ['Main Website'],
            'customer_group_ids' => ['NOT LOGGED IN'],
            'coupon_type' => 'No Coupon',
            'simple_action' => 'Percent of product price discount',
            'discount_amount' => '50'
        ];

        $this->_data['active_sales_rule_for_all_groups'] = [
            'name' => 'Shopping Cart Price Rule with Specific Coupon %isolation%',
            'description' => 'Description for Cart Price Rule',
            'is_active' => 'Active',
            'website_ids' => ['Main Website'],
            'customer_group_ids' => ['NOT LOGGED IN', 'General', 'Wholesale', 'Retailer'],
            'coupon_type' => 'Specific Coupon',
            'coupon_code' => '123-abc-ABC-987-%isolation%',
            'simple_action' => 'Percent of product price discount',
            'discount_amount' => '50',
            'store_labels' => [
                0 => 'Shopping Cart price Rule with Specific Coupon',
                1 => 'Shopping Cart price Rule with Specific Coupon'
            ],
        ];
    }
}
