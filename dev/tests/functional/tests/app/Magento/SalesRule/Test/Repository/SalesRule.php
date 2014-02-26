<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
    public function __construct(array $defaultConfig = array(), array $defaultData = array())
    {
        $this->_data['default'] = array('config' => $defaultConfig, 'data' => $defaultData);

        $this->_data[self::SIMPLE] = $this->_getSalesRuleSimple();
        $this->_data[self::ACTIONS] = $this->_getSalesRuleActions();
    }

    /**
     * Get Action data
     * @return array
     */
    protected function _getSalesRuleActions()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'discount_amount' => array(
                        'value' => '50',
                        'group' => self::GROUP_ACTIONS
                    )
                )
            )
        );
    }

    /**
     * Get Simple Data
     * @return array
     */
    protected function _getSalesRuleSimple()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'name' => array(
                        'value' => 'Simple Cart Price Rule %isolation%',
                        'group' => self::GROUP_RULE_INFORMATION
                    ),
                    'website_ids' => array(
                        'value' => array('Main Website'),
                        'group' => self::GROUP_RULE_INFORMATION,
                        'input' => 'multiselect',
                        'input_value' => array('1')
                    ),
                    'customer_group_ids' => array(
                        'value' => array('NOT LOGGED IN', 'General', 'Wholesale', 'Retailer'),
                        'group' => self::GROUP_RULE_INFORMATION,
                        'input' => 'multiselect',
                        'input_value' => array('0', '1', '2', '3')
                    )
                )
            )
        );
    }
}
