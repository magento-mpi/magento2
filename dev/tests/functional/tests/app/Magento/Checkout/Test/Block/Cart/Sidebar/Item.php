<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Block\Cart\Sidebar;

use Magento\Checkout\Test\Block\Cart\Sidebar;

/**
 * Class MiniCartItem
 * Product item block on mini Cart
 */
class Item extends Sidebar
{
    /**
     * Selector for "Remove item" button
     *
     * @var string
     */
    protected $removeItem = '.action.delete';

    /**
     * Remove product item from mini cart
     *
     * @return void
     */
    public function removeItemFromMiniCart()
    {
        $this->_rootElement->find($this->removeItem)->click();
        $this->_rootElement->acceptAlert();
    }
}
