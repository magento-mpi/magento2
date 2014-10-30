<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reminder\Test\Block\Adminhtml\Reminder\Edit\Customers;

use Mtf\Client\Element;

/**
 * Customer grid on "Matched Customers" tab.
 */
class CustomersGrid extends \Magento\Backend\Test\Block\Widget\Grid
{
    /**
     * Filters array mapping.
     *
     * @var array
     */
    protected $filters = [
        'email' => [
            'selector' => 'input[name="grid_email"]'
        ],
        'coupon' => [
            'selector' => 'input[name="grid_code"]'
        ]
    ];
}
