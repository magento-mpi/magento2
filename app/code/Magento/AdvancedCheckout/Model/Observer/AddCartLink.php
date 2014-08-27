<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdvancedCheckout\Model\Observer;

use \Magento\AdvancedCheckout\Model\Cart;

class AddCartLink
{
    /**
     * @var Cart
     */
    protected $_cart;

    /**
     * @param Cart $cart
     */
    public function __construct(Cart $cart)
    {
        $this->_cart = $cart;
    }

    /**
     * Add link to cart in cart sidebar to view grid with failed products
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (!$block instanceof \Magento\Checkout\Block\Cart\Sidebar) {
            return;
        }

        $failedItemsCount = count($this->_cart->getFailedItems());
        if ($failedItemsCount > 0) {
            $block->setAllowCartLink(true);
            $block->setCartEmptyMessage(__('%1 item(s) need your attention.', $failedItemsCount));
        }
    }
}
