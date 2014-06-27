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
 * Class CustomerSegment
 * Data for creation CustomerSegment
 */
class CustomerSegment extends AbstractRepository
{
    /**
     * @constructor
     * @param array $defaultConfig
     * @param array $defaultData
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['active_customer_segment'] = [
            'name' => 'Test Customer Segment %isolation%',
            'description' => 'Test Customer Segment Description %isolation%',
            'website_ids' => [
                0 => 'Main Website',
            ],
            'is_active' => 'Active',
            'apply_to' => 'Visitors and Registered Customers',
        ];

        $this->_data['not_active_customer_segment'] = [
            'name' => 'Test Customer Segment %isolation%',
            'description' => 'Test Customer Segment Description %isolation%',
            'website_ids' => [
                0 => 'Main Website',
            ],
            'is_active' => 'Inactive',
            'apply_to' => 'Registered Customers',
        ];
    }
}
