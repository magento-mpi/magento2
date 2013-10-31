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

namespace Magento\Sales\Test\Block\Backend\Order;

use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Grid for adding products for order in backend
 *
 * @package Magento\Sales\Test\Block\Backend\Order
 */
class ProductsAddGrid extends Grid
{
    /**
     * Initialize block elements
     */
    protected function _init()
    {
        parent::_init();
        $this->selectItem = 'tbody tr .col-in_products';
        $this->filters = array(
            'sku' => array(
                'selector' => '#sales_order_create_search_grid_filter_sku'
            ),
        );
    }

    /**
     * Add selected products to order
     */
    public function addSelectedProducts()
    {
        $this->_rootElement->find('.actions button')->click();
        $this->_templateBlock->waitLoader();
    }
}
