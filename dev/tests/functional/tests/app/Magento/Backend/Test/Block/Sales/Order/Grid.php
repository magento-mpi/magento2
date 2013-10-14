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
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
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
            'id' => '#sales_order_grid_filter_real_order_id'
        );
    }
}
