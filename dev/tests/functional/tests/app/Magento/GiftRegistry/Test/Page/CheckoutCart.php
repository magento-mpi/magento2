<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Page;

/**
 * Class CheckoutCart
 */
class CheckoutCart extends \Magento\Checkout\Test\Page\CheckoutCart
{
    const MCA = 'giftregistry/checkout/cart';

    /**
     * Initialize blocks
     * @return void
     */
    protected function _init()
    {
        $this->_blocks['giftRegistryCart'] = [
            'name' => 'giftRegistryCart',
            'class' => 'Magento\GiftRegistry\Test\Block\Cart\Link',
            'locator' => '#add-cart-items-to-gift-registry',
            'strategy' => 'css selector',
        ];
    }

    /**
     * @return \Magento\GiftRegistry\Test\Block\Cart\Link
     */
    public function getGiftRegistryCart()
    {
        return $this->getBlockInstance('giftRegistryCart');
    }
}
