<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class TargetRule
 */
class TargetRule extends AbstractRepository
{
    /**
     * @param array $defaultConfig
     * @param array $defaultData
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['target_rule_related_products'] = [
            'name' => 'TargetRuleRelatedProducts%isolation%',
            'is_active' => 'Active',
            'apply_to' => 'Related Products',
            'from_date' => ['pattern' => '04/16/2014'],
            'to_date' => ['pattern' => '09/30/2024'],
            'use_customer_segment' => 'All',
            'conditions_serialized' => '[Category|is|2]',
            'actions_serialized' => '[Category|is|the Child of the Matched Product Categories]',
        ];

        $this->_data['target_rule_up_sells'] = [
            'name' => 'TargetRuleUpSells%isolation%',
            'is_active' => 'Active',
            'apply_to' => 'Up-sells',
            'use_customer_segment' => 'All',
            'actions_serialized' => '[Category|is|the Same as Matched Product Categories]',
        ];

        $this->_data['target_rule_cross_sells'] = [
            'name' => 'TargetRuleCrossSells%isolation%',
            'is_active' => 'Active',
            'apply_to' => 'Cross-sells',
            'from_date' => ['pattern' => '04/16/2014'],
            'to_date' => ['pattern' => '09/30/2024'],
            'use_customer_segment' => 'All',
            'conditions_serialized' => '[Attribute Set|is|Default]',
            'actions_serialized' => '[Price (percentage)|equal to|100]',
        ];

        $this->_data['target_rule_related_products_with_placeholders'] = [
            'name' => 'TargetRuleRelatedProducts%isolation%',
            'is_active' => 'Active',
            'apply_to' => 'Related Products',
            'from_date' => ['pattern' => '04/16/2014'],
            'to_date' => ['pattern' => '09/30/2024'],
            'use_customer_segment' => 'All',
            'conditions_serialized' => '[Category|is|%category_1%]',
            'actions_serialized' => '[Category|is|Constant Value|%category_2%]'
                . '[Category|is|the Child of the Matched Product Categories]',
        ];

        $this->_data['target_rule_up_sells_with_placeholders'] = [
            'name' => 'TargetRuleUpSells%isolation%',
            'is_active' => 'Active',
            'apply_to' => 'Up-sells',
            'use_customer_segment' => 'All',
            'conditions_serialized' => '[Category|is|%category_1%]',
            'actions_serialized' => '[Category|is|Constant Value|%category_2%]'
                . '[Category|is|the Same as Matched Product Categories]',
        ];

        $this->_data['target_rule_cross_sells_with_placeholders'] = [
            'name' => 'TargetRuleCrossSells%isolation%',
            'is_active' => 'Active',
            'apply_to' => 'Cross-sells',
            'from_date' => ['pattern' => '04/16/2014'],
            'to_date' => ['pattern' => '09/30/2024'],
            'use_customer_segment' => 'All',
            'conditions_serialized' => '[Category|is|%category_1%][Attribute Set|is|Default]',
            'actions_serialized' => '[Category|is|Constant Value|%category_2%][Price (percentage)|equal to|100]',
        ];
    }
}
