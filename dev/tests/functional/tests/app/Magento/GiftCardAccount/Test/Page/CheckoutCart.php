<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Page;

use Magento\Checkout\Test\Page\CheckoutCart as AbstractCheckoutCart;

/**
 * Class CheckoutCartInjectable
 * Page of checkout page
 */
class CheckoutCart extends AbstractCheckoutCart
{
    // TODO: remove "index" after fix in old test generate factory
    const MCA = 'checkout/cart/index';

    protected $_blocks = [
        'giftCardAccountBlock' => [
            'name' => 'giftCardAccount',
            'class' => 'Magento\GiftCardAccount\Test\Block\Checkout\Cart\Giftcardaccount',
            'locator' => '.giftcard',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'name' => 'messages',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '.page.messages',
            'strategy' => 'css selector',
        ],
        'proceedToCheckoutBlock' => [
            'name' => 'proceedToCheckoutBlock',
            'class' => 'Magento\Checkout\Test\Block\Cart\ProceedToCheckout',
            'locator' => 'ul.checkout.methods.items',
            'strategy' => 'css selector',
        ],
        'cartBlock' => [
            'name' => 'cartBlock',
            'class' => 'Magento\Checkout\Test\Block\Cart',
            'locator' => '//div[contains(@class, "cart container")]',
            'strategy' => 'xpath',
        ],
    ];

    /**
     * @return \Magento\Checkout\Test\Block\Cart
     */
    public function getCartBlock()
    {
        return $this->getBlockInstance('cartBlock');
    }

    /**
     * @return \Magento\GiftCardAccount\Test\Block\Checkout\Cart\Giftcardaccount
     */
    public function getGiftCardAccountBlock()
    {
        return $this->getBlockInstance('giftCardAccountBlock');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }

    /**
     * @return \Magento\Checkout\Test\Block\Cart\ProceedToCheckout
     */
    public function getProceedToCheckoutBlock()
    {
        return $this->getBlockInstance('proceedToCheckoutBlock');
    }
}
