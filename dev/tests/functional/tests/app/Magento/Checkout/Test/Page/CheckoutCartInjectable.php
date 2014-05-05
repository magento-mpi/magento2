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
 * Class CheckoutCartInjectable
 *
 * @package Magento\Checkout\Test\Page
 */
class CheckoutCartInjectable extends FrontendPage
{
    const MCA = 'checkout/cart';

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
