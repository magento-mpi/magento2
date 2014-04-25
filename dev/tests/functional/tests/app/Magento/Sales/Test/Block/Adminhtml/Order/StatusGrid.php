<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order;

use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class StatusGrid
 * Adminhtml sales order's status managment grid
 *
 * @package Magento\Sales\Test\Block\Adminhtml\Order
 */
class StatusGrid extends Grid
{
    /**
     * Initialize block elements
     */
    protected $filters = [
        'label' => [
            'selector' => '#sales_order_status_grid_filter_label'
        ],
        'status' => [
            'selector' => '#sales_order_status_grid_filter_status'
        ]
    ];
}
