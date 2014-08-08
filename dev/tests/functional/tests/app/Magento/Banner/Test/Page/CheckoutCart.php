<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Page;

use Magento\Checkout\Test\Page\CheckoutCart as AbstractCheckoutCart;

/**
 * Class CheckoutCart
 */
class CheckoutCart extends AbstractCheckoutCart
{
    const MCA = 'checkout/cart';

    protected $_blocks = [
        'cartBlock' => [
            'name' => 'cartBlock',
            'class' => 'Magento\Banner\Test\Block\Cart',
            'locator' => '//div[contains(@class, "column main")]',
            'strategy' => 'xpath',
        ],
        'messagesBlock' => [
            'name' => 'messagesBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '.messages .messages',
            'strategy' => 'css selector',
        ],
        'shippingBlock' => [
            'name' => 'shippingBlock',
            'class' => 'Magento\Checkout\Test\Block\Cart\Shipping',
            'locator' => '.block.shipping',
            'strategy' => 'css selector',
        ],
        'totalsBlock' => [
            'name' => 'totalsBlock',
            'class' => 'Magento\Checkout\Test\Block\Cart\Totals',
            'locator' => '#shopping-cart-totals-table',
            'strategy' => 'css selector',
        ],
        'crosssellBlock' => [
            'name' => 'crosssellBlock',
            'class' => 'Magento\Catalog\Test\Block\Product\ProductList\Crosssell',
            'locator' => '//div[contains(@class, "block")][contains(@class, "crosssell")]',
            'strategy' => 'xpath',
        ],
        'discountCodesBlock' => [
            'name' => 'discountCodesBlock',
            'class' => 'Magento\Checkout\Test\Block\Cart\DiscountCodes',
            'locator' => '.block.discount',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Banner\Test\Block\Cart
     */
    public function getCartBlock()
    {
        return $this->getBlockInstance('cartBlock');
    }
}
