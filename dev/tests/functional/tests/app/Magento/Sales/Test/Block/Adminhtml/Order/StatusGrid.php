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
 * @package Magento\Sales\Test\Block\Adminhtml\Order
 */
class StatusGrid extends Grid
{
    /**
     * Initialize block elements
     */
    protected $filters = array(
        'label' => array(
            'selector' => '#sales_order_status_grid_filter_label'
        ),
        'status' => array(
            'selector' => '#sales_order_status_grid_filter_status'
        )
    );

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
