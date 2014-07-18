<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\TestCase\OnePageCheckoutTest\Test;

use Magento\GiftCardAccount\Test\Page\CheckoutCart;
use Magento\GiftCardAccount\Test\Fixture\GiftCardAccount;
use Mtf\TestCase\Step\StepInterface;

/**
 * Class ProceedToCheckout
 * Proceed to checkout
 */
class ProceedToCheckout implements StepInterface
{
    /**
     * Checkout cart page
     *
     * @var CheckoutCart
     */
    protected $checkoutCart;

    /**
     * Preparing step properties
     *
     * @constructor
     * @param CheckoutCart $checkoutCart
     * @param string $giftCardAccount
     */
    public function __construct(CheckoutCart $checkoutCart, $giftCardAccount)
    {
        $this->checkoutCart = $checkoutCart;
        $this->giftCardAccount = $giftCardAccount;
    }

    /**
     * Run step that proceeding to checkout
     *
     * @return void
     */
    public function run()
    {
        if ($this->giftCardAccount instanceof GiftCardAccount) {
            $this->checkoutCart->getGiftCardAccountBlock()->addGiftCard($this->giftCardAccount->getCode());
        }

        $this->checkoutCart->getProceedToCheckoutBlock()->proceedToCheckout();
    }
}
