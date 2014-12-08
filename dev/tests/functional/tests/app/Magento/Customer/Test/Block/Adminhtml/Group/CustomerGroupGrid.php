<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Block\Adminhtml\Group;

use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class CustomerGroupGrid
 * Adminhtml customer group grid
 */
class CustomerGroupGrid extends Grid
{
    /**
     * Initialize block elements
     *
     * @var array $filters
     */
    protected $filters = [
        'code' => [
            'selector' => '#customerGroupGrid_filter_type',
        ],
    ];

    /**
     * Locator value for grid to click
     *
     * @var string
     */
    protected $editLink = 'td[data-column="time"]';
}
