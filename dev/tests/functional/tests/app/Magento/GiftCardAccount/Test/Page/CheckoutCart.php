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
        'giftCardAccount' => [
            'name' => 'giftCardAccount',
            'class' => 'Magento\GiftCardAccount\Test\Block\Checkout\Cart\Giftcardaccount',
            'locator' => '.giftcard',
            'strategy' => 'css selector',
        ],
        'messages' => [
            'name' => 'messages',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '.page.messages',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\GiftCardAccount\Test\Block\Checkout\Cart\Giftcardaccount
     */
    public function getGiftCardAccount()
    {
        return $this->getBlockInstance('giftCardAccount');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessages()
    {
        return $this->getBlockInstance('messages');
    }
}
