<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Newsletter\Test\Block\Adminhtml\Subscriber;

use Magento\Backend\Test\Block\Widget\Grid as AbstractGrid;

/**
 * Newsletter subscribers grid
 *
 */
class Grid extends AbstractGrid
{
    /**
     * Filters array mapping
     *
     * @var array
     */
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
