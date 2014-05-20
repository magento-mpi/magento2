<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Page;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class CheckoutCart
 * Checkout cart page
 *
 */
class CheckoutCart extends FrontendPage
{
    /**
     * URL for checkout cart page
     */
    const MCA = 'checkout/cart';

    protected $_blocks = [
        'cartBlock' => [
            'name' => 'cartBlock',
            'class' => 'Magento\Checkout\Test\Block\Cart',
            'locator' => '//div[contains(@class, "cart container")]',
            'strategy' => 'xpath',
        ],
        'messageBlock' => [
            'name' => 'messageBlock',
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
     * Cart totals block
     *
     * @var string
     */
    protected $totalsBlock = '#shopping-cart-totals-table';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;
    }

    /**
     * Get shopping cart block
     *
     * @return \Magento\Checkout\Test\Block\Cart
     */
    public function getCartBlock()
    {
        return $this->getBlockInstance('cartBlock');
    }

    /**
     * Get messages block
     *
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messageBlock');
    }

    /**
     * @return \Magento\Checkout\Test\Block\Cart\Shipping
     */
    public function getShippingBlock()
    {
        return $this->getBlockInstance('shippingBlock');
    }

    /**
     * Get cart totals block
     *
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
}
