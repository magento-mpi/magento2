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

namespace Magento\Sales\Test\Block\Adminhtml\Order;

use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

/**
 * Class Grid
 * Sales order grid
 *
 * @package Magento\Sales\Test\Block\Adminhtml\Order
 */
class Grid extends GridInterface
{
    /**
     * 'Add New' order button
     *
     * @var string
     */
    protected $addNewOrder = "../*[@class='page-actions']//*[@id='add']";

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

    /**
     * Start to create new order
     */
    public function addNewOrder()
    {
        $this->_rootElement->find($this->addNewOrder, Locator::SELECTOR_XPATH)->click();
    }
}
