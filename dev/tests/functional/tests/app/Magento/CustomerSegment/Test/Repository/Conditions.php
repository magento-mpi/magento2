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
                    'name' => array(
                        'value' => 'All Retail Customers',
                        'group' => 'magento_customersegment_segment_tabs_general_section'
                    ),
                    'website_id' => array(
                        'value' => 'Main Website',
                        'group' => 'magento_customersegment_segment_tabs_general_section',
                        'input' => 'select',
                        'input_value' => '1'
                    ),
                    'is_active' => array(
                        'value' => 'Active',
                        'group' => 'magento_customersegment_segment_tabs_general_section',
                        'input' => 'select',
                        'input_value' => '1'
                    ),
                    'apply_to' => array(
                        'value' => 'Visitors and Registered Customers',
                        'group' => 'magento_customersegment_segment_tabs_general_section',
                        'input' => 'select',
                        'input_value' => '0'
                    )
                ),
            )
        );
    }
}