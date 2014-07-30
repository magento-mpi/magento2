<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Page;

use Magento\Checkout\Test\Page\CheckoutCart as OriginCheckoutCart;

/**
 * Class CheckoutCart
 */
class CheckoutCart extends OriginCheckoutCart
{
    const MCA = 'giftregistry/cart';

    protected $_blocks = [
        'giftRegistryCart' => [
            'name' => 'giftRegistryCart',
            'class' => 'Magento\GiftRegistry\Test\Block\Cart\Link',
            'locator' => '#add-cart-items-to-gift-registry',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\GiftRegistry\Test\Block\Cart\Link
     */
    public function getGiftRegistryCart()
    {
        return $this->getBlockInstance('giftRegistryCart');
    }
}
