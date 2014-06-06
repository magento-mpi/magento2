<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Test\Block\Cart;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Sidebar
 * Mini shopping cart block
 */
class Sidebar extends Block
{
    /**
     * Quantity input selector
     *
     * @var string
     */
    protected $qty = '.details.qty > .qty';

    /**
     * Mini cart link selector
     *
     * @var string
     */
    protected $cartLink = 'a.showcart';

    /**
     * Open mini cart
     *
     * @return void
     */
    public function openMiniCart()
    {
        $this->_rootElement->find($this->cartLink)->click();
    }

    /**
     * Get product quantity
     *
     * @return string
     */
    public function getProductQty()
    {
        $this->openMiniCart();
        return $this->_rootElement->find($this->qty, Locator::SELECTOR_CSS)->getText();
    }
}
