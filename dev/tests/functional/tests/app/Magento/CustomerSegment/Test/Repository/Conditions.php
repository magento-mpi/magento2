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

namespace Magento\CustomerSegment\Test\Repository;

use Mtf\Factory\Factory;
use Mtf\Repository\AbstractRepository;

/**
 * Class Conditions Repository
 *
 * @package Magento\CustomerSegment\Test\Fixture
 */
class Conditions extends AbstractRepository {
    /**
     * {inheritdoc}
     */
    public function __construct(array $defaultConfig, array $defaultData)
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );

        $this->_data['retailer_condition'] = $this->_getRetailerCondition();
        $this->_data['retailer_condition_curl'] = $this->_getRetailerConditionCurl();
    }

    protected function _getRetailerCondition()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'conditions__1__new_child' => array(
                        'value' => 'Group',
                        'group' => 'magento_customersegment_segment_tabs_conditions_section',
                        'input' => 'select',
                        'input_value' => 'Magento\CustomerSegment\Model\Segment\Condition\Customer\Attributes|group_id'
                    ),
                    'conditions__1--1__value' => array(
                        'value' => 'Retailer',
                        'group' => 'magento_customersegment_segment_tabs_conditions_section',
                        'input' => 'select',
                        'input_value' => '3'
                    )
                ),
            )
        );
    }

    protected function _getRetailerConditionCurl()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'segment_id' => array(
                        'value' => '%segment_id%',
                        'group' => 'magento_customersegment_segment_tabs_conditions_section'
                    ),
                    'name' => array(
                        'value' => 'All Retail Customers',
                        'group' => 'magento_customersegment_segment_tabs_conditions_section',
                        'input' => 'text'
                    ),
                    'description' => array(
                        'value' => 'Customer Segment test for retailer customers',
                        'group' => 'magento_customersegment_segment_tabs_conditions_section'
                    ),
                    'website_ids[]' => array(
                        'value' => 'Main Website',
                        'group' => 'magento_customersegment_segment_tabs_conditions_section',
                        'input' => 'select',
                        'input_value' => '1'
                    ),
                    'is_active' => array(
                        'value' => 'Active',
                        'group' => 'magento_customersegment_segment_tabs_conditions_section',
                        'input' => 'select',
                        'input_value' => '1'
                    ),
                    'rule[conditions][1][type]' => array(
                        'value' => 'Magento\CustomerSegment\Model\Segment\Condition\Combine\Root',
                        'group' => 'magento_customersegment_segment_tabs_conditions_section',
                        'input' => 'hidden'
                    ),
                    'rule[conditions][1][aggregator]' => array(
                        'value' => 'ANY',
                        'group' => 'magento_customersegment_segment_tabs_conditions_section',
                        'input' => 'select',
                        'input_value' => 'any'
                    ),
                    'rule[conditions][1][value]' => array(
                        'value' => 'TRUE',
                        'group' => 'magento_customersegment_segment_tabs_conditions_section',
                        'input' => 'select',
                        'input_value' => '1'
                    ),
                    'rule[conditions][1--1][type]' => array(
                        'value' => 'Magento\CustomerSegment\Model\Segment\Condition\Customer\Attributes',
                        'group' => 'magento_customersegment_segment_tabs_conditions_section',
                        'input' => 'hidden'
                    ),
                    'rule[conditions][1--1][attribute]' => array(
                        'value' => 'group_id',
                        'group' => 'magento_customersegment_segment_tabs_conditions_section',
                        'input' => 'hidden'
                    ),
                    'rule[conditions][1--1][operator]' => array(
                        'value' => 'is',
                        'group' => 'magento_customersegment_segment_tabs_conditions_section',
                        'input' => 'select',
                        'input_value' => '=='
                    ),
                    'rule[conditions][1--1][value]' => array(
                        'value' => 'Retailer',
                        'group' => 'magento_customersegment_segment_tabs_conditions_section',
                        'input' => 'select',
                        'input_value' => '3'
                    ),
                    'rule[conditions][1][new_child]' => array(
                        'value' => 'Group',
                        'group' => 'magento_customersegment_segment_tabs_conditions_section',
                        'input' => 'select',
                        'input_value' => 'Magento\CustomerSegment\Model\Segment\Condition\Customer\Attributes|group_id'
                    )
                ),
            )
        );
    }
}