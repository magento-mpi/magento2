<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CustomerSegment\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class GeneralProperties Repository
 *
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
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'config' => $defaultConfig,
            'data' => $defaultData,
        ];

        $this->_data['all_retail_customers'] = $this->_getRetailAll();
    }

    protected function _getRetailAll()
    {
        return [
            'data' => [
                'fields' => [
                    'name' => [
                        'value' => 'All Retail Customers %isolation%',
                        'group' => self::GENERAL_TAB_ID,
                    ],
                    'description' => [
                        'value' => 'Customer Segment test for retailer customers',
                        'group' => self::GENERAL_TAB_ID,
                    ],
                    'website_ids' => [
                        'value' => 'Main Website',
                        'group' => self::GENERAL_TAB_ID,
                        'input' => 'select',
                        'input_value' => '1',
                    ],
                    'is_active' => [
                        'value' => 'Active',
                        'group' => self::GENERAL_TAB_ID,
                        'input' => 'select',
                        'input_value' => '1',
                    ],
                    'apply_to' => [
                        'value' => 'Visitors and Registered Customers',
                        'group' => self::GENERAL_TAB_ID,
                        'input' => 'select',
                        'input_value' => '0',
                    ],
                ],
            ]
        ];
    }
}
