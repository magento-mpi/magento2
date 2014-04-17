<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Block\Adminhtml\Customer;

use Magento\Backend\Test\Block\Widget\Grid as AbstractGrid;

/**
 * Custom Customer Grid
 *
 * @package Magento\Customer\Test\Block\Adminhtml\Customer
 */
class Grid extends AbstractGrid {
    protected $filters = [
        'name' => [
            'selector' => '#customerGrid_filter_name'
        ],
        'email' => [
            'selector' => '#customerGrid_filter_email',
        ],
    ];
}
