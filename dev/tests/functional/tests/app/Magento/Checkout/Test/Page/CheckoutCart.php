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
    const MCA = 'checkout/cart/index';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'bannerCartBlock' => [
            'class' => 'Magento\Banner\Test\Block\Cart',
            'locator' => '.column.main',
            'strategy' => 'css selector',
        ],
        'cartBlock' => [
            'class' => 'Magento\Checkout\Test\Block\Cart',
            'locator' => '//div[contains(@class, "column main")]',
            'strategy' => 'xpath',
        ],
        'messagesBlock' => [
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '.messages .messages',
            'strategy' => 'css selector',
        ],
        'shippingBlock' => [
            'class' => 'Magento\Checkout\Test\Block\Cart\Shipping',
            'locator' => '.block.shipping',
            'strategy' => 'css selector',
        ],
        'totalsBlock' => [
            'class' => 'Magento\Checkout\Test\Block\Cart\Totals',
            'locator' => '#shopping-cart-totals-table',
            'strategy' => 'css selector',
        ],
        'crosssellBlock' => [
            'class' => 'Magento\Catalog\Test\Block\Product\ProductList\Crosssell',
            'locator' => '//div[contains(@class, "block")][contains(@class, "crosssell")]',
            'strategy' => 'xpath',
        ],
        'discountCodesBlock' => [
            'class' => 'Magento\Checkout\Test\Block\Cart\DiscountCodes',
            'locator' => '.block.discount',
            'strategy' => 'css selector',
        ],
        'proceedToCheckoutBlock' => [
            'class' => 'Magento\Checkout\Test\Block\Onepage\Link',
            'locator' => 'ul.checkout-methods-items button.primary.checkout',
            'strategy' => 'css selector',
        ],
        'giftCardAccountBlock' => [
            'class' => 'Magento\GiftCardAccount\Test\Block\Checkout\Cart\Giftcardaccount',
            'locator' => '.giftcard',
            'strategy' => 'css selector',
        ],
        'giftRegistryCart' => [
            'class' => 'Magento\GiftRegistry\Test\Block\Cart\Link',
            'locator' => '#add-cart-items-to-gift-registry',
            'strategy' => 'css selector',
        ],
        'checkoutTooltipBlock' => [
            'class' => 'Magento\Reward\Test\Block\Tooltip\Checkout',
            'locator' => '.rewards',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Banner\Test\Block\Cart
     */
    public function getBannerCartBlock()
    {
        return $this->getBlockInstance('bannerCartBlock');
    }

    /**
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
        return $this->getBlockInstance('proceedToCheckoutBlock');
    }

    /**
     * @return \Magento\GiftCardAccount\Test\Block\Checkout\Cart\Giftcardaccount
     */
    public function getGiftCardAccountBlock()
    {
        return $this->getBlockInstance('giftCardAccountBlock');
    }

    /**
     * @return \Magento\GiftRegistry\Test\Block\Cart\Link
     */
    public function getGiftRegistryCart()
    {
        return $this->getBlockInstance('giftRegistryCart');
    }

    /**
     * @return \Magento\Reward\Test\Block\Tooltip\Checkout
     */
    public function getCheckoutTooltipBlock()
    {
        return $this->getBlockInstance('checkoutTooltipBlock');
    }
}
