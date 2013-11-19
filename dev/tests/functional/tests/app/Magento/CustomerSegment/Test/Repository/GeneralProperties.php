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
 * Class GeneralProperties Repository
 *
 * @package Magento\CustomerSegment\Test\Fixture
 */
class GeneralProperties extends AbstractRepository {
    /**
     * {inheritdoc}
     */
    public function __construct(array $defaultConfig, array $defaultData)
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );

        $this->_data['all_retail_customers'] = $this->_getRetailAll();
    }

    protected function _getRetailAll()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'name' => array(
                        'value' => 'All Retail Customers',
                        'group' => 'magento_customersegment_segment_tabs_general_section'
                    ),
                    'description' => array(
                        'value' => 'Customer Segment test for retailer customers',
                        'group' => 'magento_customersegment_segment_tabs_general_section'
                    ),
                    'website_ids' => array(
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