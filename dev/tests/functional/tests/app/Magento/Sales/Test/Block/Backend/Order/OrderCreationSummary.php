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

use Mtf\Block\Block;

/**
 * Summary for order on oorder create page in backend
 *
 * @package Magento\Sales\Test\Block\Backend\Order
 */
class OrderCreationSummary extends Block
{
    public function clickSaveOrder()
    {
        $this->_rootElement->find('.order-totals-bottom button')->click();
    }
}
