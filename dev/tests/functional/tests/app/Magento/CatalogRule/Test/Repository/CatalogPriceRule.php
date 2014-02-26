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

namespace Magento\CatalogRule\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class CatalogPriceRule Repository
 *
 * @package Magento\CatalogRule\Test\Repository
 */
class CatalogPriceRule extends AbstractRepository
{
    const CATALOG_PRICE_RULE = 'catalog_price_rule';

    const CATALOG_PRICE_RULE_ALL_GROUPS = 'catalog_price_rule_all_groups';

    const CUSTOMER_GROUP_GENERAL_RULE = 'customer_group_general_rule';

    const GROUP_RULE_INFORMATION = 'rule_information';

    const GROUP_CONDITIONS = 'conditions';

    const GROUP_ACTIONS = 'actions';

    const CONDITION_TYPE = 'conditions__1__new_child';

    const CONDITION_VALUE = 'conditions__1--1__value';


    public function __construct(array $defaultConfig = array(), array $defaultData = array())
    {
        $this->_data['default'] = array('config' => $defaultConfig, 'data' => $defaultData);
        $this->_data[self::CATALOG_PRICE_RULE] = $this->_getCatalogPriceRule();
        $this->_data[self::CATALOG_PRICE_RULE_ALL_GROUPS] = array_replace_recursive(
            $this->_getCatalogPriceRule(),
            $this->_getCatalogPriceRuleAllGroups()
        );
    }

    protected function _getCatalogPriceRule()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'name' => array('value' => 'Rule %isolation%', 'group' => static::GROUP_RULE_INFORMATION),
                    'is_active' => array(
                        'value' => 'Active',
                        'group' => static::GROUP_RULE_INFORMATION,
                        'input' => 'select'
                    ),
                    'website_ids' => array(
                        'value' => array('Main Website'),
                        'group' => static::GROUP_RULE_INFORMATION,
                        'input' => 'multiselect',
                        'input_value' => array('1')
                    ),
                    'customer_group_ids' => array(
                        'value' => array('%group_value%'),
                        'group' => static::GROUP_RULE_INFORMATION,
                        'input' => 'multiselect',
                        'input_value' => array('%group_id%')
                    ),
                    'simple_action' => array(
                        'value' => 'By Percentage of the Original Price',
                        'group' => static::GROUP_ACTIONS,
                        'input' => 'select'
                    ),
                    'discount_amount' => array('value' => '50.0000', 'group' => static::GROUP_ACTIONS),
                    self::CONDITION_TYPE => array(
                        'value' => 'Category',
                        'group' => static::GROUP_CONDITIONS,
                        'input' => 'select',
                        'input_value' => 'Magento\CatalogRule\Model\Rule\Condition\Product|category_ids'
                    ),
                    self::CONDITION_VALUE => array(
                        'value' => '%category_id%',
                        'group' => static::GROUP_CONDITIONS,
                        'input' => 'input'
                    )
                )
            )
        );
    }

    protected function _getCatalogPriceRuleAllGroups()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'customer_group_ids' => array(
                        'value' => array('NOT LOGGED IN', 'General', 'Wholesale', 'Retailer'),
                        'group' => static::GROUP_RULE_INFORMATION,
                        'input' => 'multiselect',
                        'input_value' => array('0', '1', '2', '3')
                    )
                )
            )
        );
    }
}
