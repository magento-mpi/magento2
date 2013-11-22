<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesRule\Test\Repository;

use Mtf\Factory\Factory;
use Mtf\Repository\AbstractRepository;
use Magento\SalesRule\Test\Block\PromoQuoteForm;

class SalesRule extends AbstractRepository
{
    const SIMPLE = 'sales_rule_simple';

    const ACTIONS = 'sales_rule_actions';

    /**
     * @param array $defaultConfig
     * @param array $defaultData
     */
    public function __construct(array $defaultConfig, array $defaultData)
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );

        $this->_data[self::SIMPLE] = $this->_getSalesRuleSimple();
        $this->_data[self::ACTIONS] = $this->_getSalesRuleActions();
    }

    protected function _getSalesRuleActions()
    {
        return [
            'data' => [
                'fields' => [
                    'discount_amount' => [
                        'value' => '50',
                        'group' => PromoQuoteForm::RULE_ACTIONS_TAB
                    ]
                ]
            ]
        ];
    }

    protected function _getSalesRuleSimple()
    {
        return [
            'data' => [
                'fields' => [
                    'name' => [
                        'value' => 'Simple Cart Price Rule %isolation%',
                        'group' => PromoQuoteForm::RULE_INFO_TAB
                    ],
                    'website_ids' => [
                        'value' => 'Main Website',
                        'group' => PromoQuoteForm::RULE_INFO_TAB,
                        'input' => 'select',
                        'input_value' => '1'
                    ],
                    'customer_group_ids' => [
                        'value' => ['NOT LOGGED IN', 'General', 'Wholesale', 'Retailer'],
                        'group' => PromoQuoteForm::RULE_INFO_TAB,
                        'input' => 'multiselect',
                        'input_value' => ['0', '1', '2', '3']
                    ]
                ]
            ]
        ];
    }
}
