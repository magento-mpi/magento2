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
 * Adminhtml sales order's status management grid
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

    /**
     * Selector for unassign custom status link
     *
     * @var string
     */
    protected $unassignLink = '[data-column="unassign"] a';


    /**
     * Search custom status and unassign it
     *
     * @param array $filter
     * @throws \Exception
     * @return void
     */
    public function searchAndUnassign(array $filter)
    {
        $this->openFilterBlock();
        $this->search($filter);
        $selectItem = $this->_rootElement->find($this->unassignLink);
        if ($selectItem->isVisible()) {
            $selectItem->click();
        } else {
            throw new \Exception('Searched item was not found.');
        }
    }
}
