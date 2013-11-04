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

namespace Magento\Backend\Test\Block\Sales\Order;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

/**
 * Class Grid
 * Sales order grid
 *
 * @package Magento\Backend\Test\Block\Sales\Order
 */
class Grid extends GridInterface
{
    /**
     * Initialize block elements
     */
    protected function _init()
    {
        parent::_init();
        $this->filters = array(
            'id' => array(
                'selector' => '#sales_order_grid_filter_real_order_id'
            ),
            'status' => array(
                'selector' => '#sales_order_grid_filter_status',
                'input' => 'select'
            )
        );
    }
}
