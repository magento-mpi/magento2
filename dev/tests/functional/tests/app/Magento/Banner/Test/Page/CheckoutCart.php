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
    const MCA = 'banner/checkout/cart';

    protected $_blocks = [
        'cartBlock' => [
            'name' => 'cartBlock',
            'class' => 'Magento\Banner\Test\Block\Cart',
            'locator' => '//div[contains(@class, "column main")]',
            'strategy' => 'xpath',
        ]
    ];

    /**
     * @return \Magento\Banner\Test\Block\Cart
     */
    public function getCartBlock()
    {
        return $this->getBlockInstance('cartBlock');
    }
}
