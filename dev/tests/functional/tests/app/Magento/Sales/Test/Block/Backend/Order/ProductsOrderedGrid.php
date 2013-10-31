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
 * Grid for products already present in order during it creation in backend
 *
 * @package Magento\Sales\Test\Block\Backend\Order
 */
class ProductsOrderedGrid extends Grid
{
    /**
     * Click create new customer button
     */
    public function addNewProduct()
    {
        $this->_rootElement->find('.actions .action-add')->click();
    }
}
