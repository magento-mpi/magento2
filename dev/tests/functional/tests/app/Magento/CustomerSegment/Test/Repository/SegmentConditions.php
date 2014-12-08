<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class Conditions Repository
 *
 */
class SegmentConditions extends AbstractRepository
{
    /**
     *  Conditions Tab html Id
     */
    const CONDITIONS_TAB_ID = 'magento_customersegment_segment_tabs_conditions_section';

    /**
     * {@inheritdoc}
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'config' => $defaultConfig,
            'data' => $defaultData,
        ];

        $this->_data['retailer_condition'] = $this->_getRetailerCondition();
        $this->_data['retailer_condition_curl'] = $this->_getRetailerConditionCurl();
    }

    protected function _getRetailerCondition()
    {
        return [
            'data' => [
                'fields' => [
                    'conditions__1__new_child' => [
                        'value' => 'Group',
                        'group' => self::CONDITIONS_TAB_ID,
                        'input' => 'select',
                        'input_value' => 'Magento\CustomerSegment\Model\Segment\Condition\Customer\Attributes|group_id',
                    ],
                    'conditions__1--1__value' => [
                        'value' => 'Retailer',
                        'group' => self::CONDITIONS_TAB_ID,
                        'input' => 'select',
                        'input_value' => '3',
                    ],
                ],
            ]
        ];
    }

    protected function _getRetailerConditionCurl()
    {
        return [
            'data' => [
                'fields' => [
                    'segment_id' => [
                        'value' => '%segment_id%',
                        'group' => self::CONDITIONS_TAB_ID,
                    ],
                    'name' => [
                        'value' => '%name%',
                        'group' => self::CONDITIONS_TAB_ID,
                        'input' => 'text',
                    ],
                    'description' => [
                        'value' => 'Customer Segment test for retailer customers',
                        'group' => self::CONDITIONS_TAB_ID,
                    ],
                    'website_ids[]' => [
                        'value' => 'Main Website',
                        'group' => self::CONDITIONS_TAB_ID,
                        'input' => 'select',
                        'input_value' => '1',
                    ],
                    'is_active' => [
                        'value' => 'Active',
                        'group' => self::CONDITIONS_TAB_ID,
                        'input' => 'select',
                        'input_value' => '1',
                    ],
                    'rule[conditions][1][type]' => [
                        'value' => 'Magento\CustomerSegment\Model\Segment\Condition\Combine\Root',
                        'group' => self::CONDITIONS_TAB_ID,
                        'input' => 'hidden',
                    ],
                    'rule[conditions][1][aggregator]' => [
                        'value' => 'ANY',
                        'group' => self::CONDITIONS_TAB_ID,
                        'input' => 'select',
                        'input_value' => 'any',
                    ],
                    'rule[conditions][1][value]' => [
                        'value' => 'TRUE',
                        'group' => self::CONDITIONS_TAB_ID,
                        'input' => 'select',
                        'input_value' => '1',
                    ],
                    'rule[conditions][1--1][type]' => [
                        'value' => 'Magento\CustomerSegment\Model\Segment\Condition\Customer\Attributes',
                        'group' => self::CONDITIONS_TAB_ID,
                        'input' => 'hidden',
                    ],
                    'rule[conditions][1--1][attribute]' => [
                        'value' => 'group_id',
                        'group' => self::CONDITIONS_TAB_ID,
                        'input' => 'hidden',
                    ],
                    'rule[conditions][1--1][operator]' => [
                        'value' => 'is',
                        'group' => self::CONDITIONS_TAB_ID,
                        'input' => 'select',
                        'input_value' => '==',
                    ],
                    'rule[conditions][1--1][value]' => [
                        'value' => 'Retailer',
                        'group' => self::CONDITIONS_TAB_ID,
                        'input' => 'select',
                        'input_value' => '3',
                    ],
                    'rule[conditions][1][new_child]' => [
                        'value' => 'Group',
                        'group' => self::CONDITIONS_TAB_ID,
                        'input' => 'select',
                        'input_value' => 'Magento\CustomerSegment\Model\Segment\Condition\Customer\Attributes|group_id',
                    ],
                ],
            ]
        ];
    }
}
