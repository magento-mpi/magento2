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
     * Constructor
     *
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
            'website_ids' => ['Main Website'],
            'is_active' => 'Active',
            'apply_to' => 'Visitors and Registered Customers',
        ];

        $this->_data['not_active_customer_segment'] = [
            'name' => 'Test Customer Segment %isolation%',
            'description' => 'Test Customer Segment Description %isolation%',
            'website_ids' => ['Main Website'],
            'is_active' => 'Inactive',
            'apply_to' => 'Registered Customers',
        ];

        $this->_data['active_customer_segment_with_billing_address'] = [
            'name' => 'Test Customer Segment %isolation%',
            'description' => 'Test Customer Segment Description %isolation%',
            'website_ids' => ['Main Website'],
            'is_active' => 'Active',
            'apply_to' => 'Registered Customers',
            'conditions_serialized' =>'[Default Billing Address|exists]',
        ];

        $this->_data['active_customer_segment_with_shipping_address'] = [
            'name' => 'Test Customer Segment %isolation%',
            'description' => 'Test Customer Segment Description %isolation%',
            'website_ids' => ['Main Website'],
            'is_active' => 'Active',
            'apply_to' => 'Registered Customers',
            'conditions_serialized' =>'[Default Shipping Address|exists]',
        ];
    }
}
