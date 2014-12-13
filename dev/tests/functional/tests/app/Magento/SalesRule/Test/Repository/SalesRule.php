<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\SalesRule\Test\Repository;

use Mtf\Repository\AbstractRepository;

class SalesRule extends AbstractRepository
{
    /**
     * Tabs
     */
    const GROUP_RULE_INFORMATION = 'rule_information';
    const GROUP_ACTIONS = 'actions';

    /**
     * Key for simple data
     */
    const SIMPLE = 'sales_rule_simple';

    /**
     * Key for action data
     */
    const ACTIONS = 'sales_rule_actions';

    /**
     * @param array $defaultConfig
     * @param array $defaultData
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = ['config' => $defaultConfig, 'data' => $defaultData];

        $this->_data[self::SIMPLE] = $this->_getSalesRuleSimple();
        $this->_data[self::ACTIONS] = $this->_getSalesRuleActions();
    }

    /**
     * Get Action data
     * @return array
     */
    protected function _getSalesRuleActions()
    {
        return [
            'data' => [
                'fields' => [
                    'discount_amount' => [
                        'value' => '50',
                        'group' => self::GROUP_ACTIONS,
                    ],
                ],
            ]
        ];
    }

    /**
     * Get Simple Data
     * @return array
     */
    protected function _getSalesRuleSimple()
    {
        return [
            'data' => [
                'fields' => [
                    'name' => [
                        'value' => 'Simple Cart Price Rule %isolation%',
                        'group' => self::GROUP_RULE_INFORMATION,
                    ],
                    'website_ids' => [
                        'value' => ['Main Website'],
                        'group' => self::GROUP_RULE_INFORMATION,
                        'input' => 'multiselect',
                        'input_value' => ['1'],
                    ],
                    'customer_group_ids' => [
                        'value' => ['NOT LOGGED IN', 'General', 'Wholesale', 'Retailer'],
                        'group' => self::GROUP_RULE_INFORMATION,
                        'input' => 'multiselect',
                        'input_value' => ['0', '1', '2', '3'],
                    ],
                ],
            ]
        ];
    }
}
