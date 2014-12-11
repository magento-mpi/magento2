<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Reminder\Test\Block\Adminhtml\Reminder\Edit\Customers;


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
            'selector' => 'input[name="grid_email"]',
        ],
        'coupon' => [
            'selector' => 'input[name="grid_code"]',
        ],
    ];
}
