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
class SegmentGeneralProperties extends AbstractRepository
{
    /**
     *  Conditions Tab html Id
     */
    const GENERAL_TAB_ID = 'general_properties';

    /**
     * {@inheritdoc}
     */
    public function __construct(array $defaultConfig = array(), array $defaultData = array())
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
                        'value' => 'All Retail Customers %isolation%',
                        'group' => self::GENERAL_TAB_ID
                    ),
                    'description' => array(
                        'value' => 'Customer Segment test for retailer customers',
                        'group' => self::GENERAL_TAB_ID
                    ),
                    'website_ids' => array(
                        'value' => 'Main Website',
                        'group' => self::GENERAL_TAB_ID,
                        'input' => 'select',
                        'input_value' => '1'
                    ),
                    'is_active' => array(
                        'value' => 'Active',
                        'group' => self::GENERAL_TAB_ID,
                        'input' => 'select',
                        'input_value' => '1'
                    ),
                    'apply_to' => array(
                        'value' => 'Visitors and Registered Customers',
                        'group' => self::GENERAL_TAB_ID,
                        'input' => 'select',
                        'input_value' => '0'
                    )
                ),
            )
        );
    }
}
