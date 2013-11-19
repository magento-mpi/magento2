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
}