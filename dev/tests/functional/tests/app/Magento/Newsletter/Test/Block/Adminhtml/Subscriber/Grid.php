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

namespace Magento\Newsletter\Test\Block\Adminhtml\Subscriber;

use Magento\Backend\Test\Block\Widget\Grid as AbstractGrid;

/**
 * Custom Newsletter Subscriber Grid
 *
 * @package Magento\Newsletter\Test\Block\Adminhtml\Subscriber
 */
class Grid extends AbstractGrid {
    protected $filters = [
        'email' => [
            'selector' => '#subscriberGrid_filter_email',
        ],
        'firstname' => [
            'selector' => '#subscriberGrid_filter_firstname'
        ],
        'lastname' => [
            'selector' => '#subscriberGrid_filter_lastname'
        ],
        'status' => [
            'selector' => '#subscriberGrid_filter_status',
            'input' => 'select'
        ],
    ];
}
