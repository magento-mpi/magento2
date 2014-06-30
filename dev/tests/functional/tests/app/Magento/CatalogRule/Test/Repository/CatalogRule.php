<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class CatalogRule
 */
class CatalogRule extends AbstractRepository
{
    /**
     * @param array $defaultConfig
     * @param array $defaultData
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['active_catalog_rule'] = [
            'name' => 'Active Catalog Rule',
            'description' => 'Rule Description',
            'is_active' => 'Active',
            'website_ids' => [
                0 => 'Main Website'
            ],
            'customer_group_ids' => [
                0 => 'NOT LOGGED IN',
                1 => 'General',
                2 => 'Wholesale',
                3 => 'Retailer',
            ],
            'from_date' => '3/25/14',
            'to_date' => '3/29/14',
            'sort_order' => '1',
            'simple_action' => 'By Percentage of the Original Price',
            'discount_amount' => '50'
        ];

        $this->_data['inactive_catalog_price_rule'] = [
            'name' => 'Inactive Catalog Price Rule',
            'is_active' => 'Inactive',
            'website_ids' => [
                0 => 'Main Website'
            ],
            'customer_group_ids' => [0 => 'NOT LOGGED IN'],
            'simple_action' => 'By Percentage of the Original Price',
            'discount_amount' => '50'
        ];

        $this->_data['active_catalog_price_rule_with_conditions'] = [
            'name' => 'Active Catalog Rule with conditions %isolation%',
            'description' => 'Rule Description',
            'is_active' => 'Active',
            'website_ids' => [
                0 => 'Main Website'
            ],
            'customer_group_ids' => [
                0 => 'NOT LOGGED IN',
                1 => 'General',
                2 => 'Wholesale',
                3 => 'Retailer',
            ],
            'rule' => '[Category|is|2]',
            'simple_action' => 'By Percentage of the Original Price',
            'discount_amount' => '10'
        ];
    }
}
