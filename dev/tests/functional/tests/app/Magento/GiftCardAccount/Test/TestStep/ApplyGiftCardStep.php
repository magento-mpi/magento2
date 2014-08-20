<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\TestStep;

use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\GiftCardAccount\Test\Fixture\GiftCardAccount;
use Mtf\TestStep\TestStepInterface;

/**
 * Class ApplyGiftCardStep
 * Apply gift card before one page checkout
 */
class ApplyGiftCardStep implements TestStepInterface
{
    /**
     * Checkout cart page
     *
     * @var CheckoutCart
     */
    protected $checkoutCart;

    /**
     * GiftCardAccount fixture
     *
     * @var GiftCardAccount
     */
    protected $giftCardAccount;

    /**
     * @constructor
     * @param CheckoutCart $checkoutCart
     * @param GiftCardAccount $giftCardAccount
     */
    public function __construct(CheckoutCart $checkoutCart, GiftCardAccount $giftCardAccount = null)
    {
        $this->checkoutCart = $checkoutCart;
        $this->giftCardAccount = $giftCardAccount;
    }

    /**
     * Apply gift card before one page checkout
     *
     * @return void
     */
    public function run()
    {
        if ($this->giftCardAccount != null) {
            $this->checkoutCart->getGiftCardAccountBlock()->addGiftCard($this->giftCardAccount->getCode());
        }
    }
}
