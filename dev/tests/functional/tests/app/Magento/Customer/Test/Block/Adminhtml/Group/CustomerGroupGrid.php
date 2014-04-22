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
 *
 * @package Magento\Catalog\Test\Block
 */
class CustomerGroupGrid extends Grid
{
    /**
     * Initialize block elements
     *
     * @var array $filters
     */
    protected $filters = [
        'type' => [
            'selector' => '#customerGroupGrid_filter_type'
        ]
    ];

    /**
     * Update attributes for selected items
     *
     * @param array $items
     */
    public function updateAttributes(array $items = array())
    {
        $this->massaction('Update Attributes', $items);
    }
}
