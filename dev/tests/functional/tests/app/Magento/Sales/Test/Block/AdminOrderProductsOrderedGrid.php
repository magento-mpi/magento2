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

namespace Magento\Sales\Test\Block;

use Magento\Backend\Test\Block\Widget\Grid;

class AdminOrderProductsOrderedGrid extends Grid
{
    /**
     * Click create new customer button
     */
    public function addNewProduct()
    {
        $this->_rootElement->find('.actions .action-add')->click();
    }
}
