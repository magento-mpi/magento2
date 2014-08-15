<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Page;

use Mtf\Page\FrontendPage;

/**
 * Class CheckoutCart
 */
class CheckoutCart extends FrontendPage
{
    const MCA = 'checkout/cart';

    protected $_blocks = [
        'cartBlock' => [
            'name' => 'cartBlock',
            'class' => 'Magento\Checkout\Test\Block\Cart',
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
        'link' => [
            'name' => 'link',
            'class' => 'Magento\Checkout\Test\Block\Onepage\Link',
            'locator' => 'ul.checkout-methods-items button.primary.checkout',
            'strategy' => 'css selector',
        ],
    ];

    /**s
     * @return \Magento\Checkout\Test\Block\Cart
     */
    public function getCartBlock()
    {
        return $this->getBlockInstance('cartBlock');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }

    /**
     * @return \Magento\Checkout\Test\Block\Cart\Shipping
     */
    public function getShippingBlock()
    {
        return $this->getBlockInstance('shippingBlock');
    }

    /**
     * @return \Magento\Checkout\Test\Block\Cart\Totals
     */
    public function getTotalsBlock()
    {
        return $this->getBlockInstance('totalsBlock');
    }

    /**
     * @return \Magento\Catalog\Test\Block\Product\ProductList\Crosssell
     */
    public function getCrosssellBlock()
    {
        return $this->getBlockInstance('crosssellBlock');
    }

    /**
     * @return \Magento\Checkout\Test\Block\Cart\DiscountCodes
     */
    public function getDiscountCodesBlock()
    {
        return $this->getBlockInstance('discountCodesBlock');
    }

    /**
     * @return \Magento\Checkout\Test\Block\Onepage\Link
     */
    public function getProceedToCheckoutBlock()
    {
        return $this->getBlockInstance('link');
    }
}
