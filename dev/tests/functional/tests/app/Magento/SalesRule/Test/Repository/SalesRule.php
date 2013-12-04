<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Test\Repository;

use Mtf\Repository\AbstractRepository;
use Magento\SalesRule\Test\Block\Adminhtml\Promo\Quote\Edit\Form;

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
        $this->_data['default'] = array('config' => $defaultConfig, 'data' => $defaultData);

        $this->_data[self::SIMPLE] = $this->_getSalesRuleSimple();
        $this->_data[self::ACTIONS] = $this->_getSalesRuleActions();
    }

    protected function _getSalesRuleActions()
    {
        return array(
            'data' => array(
                'fields' => array('discount_amount' => array('value' => '50', 'group' => Form::RULE_ACTIONS_TAB))
            )
        );
    }

    protected function _getSalesRuleSimple()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'name' => array('value' => 'Simple Cart Price Rule %isolation%', 'group' => Form::RULE_MAIN_TAB),
                    'website_ids' => array(
                        'value' => 'Main Website',
                        'group' => Form::RULE_MAIN_TAB,
                        'input' => 'select',
                        'input_value' => '1'
                    ),
                    'customer_group_ids' => array(
                        'value' => array('NOT LOGGED IN', 'General', 'Wholesale', 'Retailer'),
                        'group' => Form::RULE_MAIN_TAB,
                        'input' => 'multiselect',
                        'input_value' => array('0', '1', '2', '3')
                    )
                )
            )
        );
    }
}
