<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\Fixture;

use Mtf\Fixture\DataFixture;

/**
 * Class CatalogPriceRule
 *
 * @package Magento\CatalogRule\Test\Fixture
 */
class CatalogPriceRule extends DataFixture
{
    /**
     * Groups for mapping data into ui tabs
     */
    const GROUP_RULE_INFORMATION = 'promo_catalog_edit_tabs_main_section';
    const GROUP_CONDITIONS = 'promo_catalog_edit_tabs_conditions_section';
    const GROUP_ACTIONS = 'promo_catalog_edit_tabs_actions_section';

    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        $this->_data = array(
            'fields' => array(
                'rule_name' => array(
                    'value' => 'rule1',
                    'group' => static::GROUP_RULE_INFORMATION
                ),
                'rule_is_active' => array(
                    'value' => 'Active',
                    'group' => static::GROUP_RULE_INFORMATION,
                    'input' => 'select'
                ),
                'rule_website_ids' => array(
                    'value' => ['Main Website'],
                    'group' => static::GROUP_RULE_INFORMATION,
                    'input' => 'multiselect',
                    'input_value' => ['1']
                ),
                'rule_customer_group_ids' => array(
                    'value' => ['NOT LOGGED IN', 'General', 'Wholesale', 'Retailer'],
                    'group' => static::GROUP_RULE_INFORMATION,
                    'input' => 'multiselect',
                    'input_value' => ['0', '1', '2', '3']
                ),
                'rule_simple_action' => array(
                    'value' => 'By Percentage of the Original Price',
                    'group' => static::GROUP_ACTIONS,
                    'input' => 'select'
                ),
                'rule_discount_amount' => array(
                    'value' => '50.0000',
                    'group' => static::GROUP_ACTIONS,
                ),
                'conditions__1__new_child' => array(
                    'value' => 'Category',
                    'group' => static::GROUP_CONDITIONS,
                    'input' => 'select',
                    'input_value' => 'Magento\CatalogRule\Model\Rule\Condition\Product|category_ids'
                ),
                'conditions__1--1__value' => array(
                    'value' => '%category_id%',
                    'group' => static::GROUP_CONDITIONS,
                    'input' => 'input'
                )
            )
        );
    }

    public function getRuleName()
    {
        return $this->getData('fields/rule_name/value');
    }
}
