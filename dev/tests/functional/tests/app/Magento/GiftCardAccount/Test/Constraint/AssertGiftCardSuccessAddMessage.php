<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\GiftCardAccount\Test\Fixture\GiftCardAccount;

/**
 * Class AssertGiftCardSuccessAddMessage
 * Assert that success message is displayed
 */
class AssertGiftCardSuccessAddMessage extends AbstractConstraint
{
    /**
     * Text value to be checked
     */
    const SUCCESS_APPLY_MESSAGE = 'Gift Card "%s" was added.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that success apply message is displayed on "Shopping Cart" frontend page
     *
     * @param CheckoutCart $checkoutCart
     * @param GiftCardAccount $giftCardAccount
     * @return void
     */
    public function processAssert(CheckoutCart $checkoutCart, GiftCardAccount $giftCardAccount)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            sprintf(self::SUCCESS_APPLY_MESSAGE, $giftCardAccount->getCode()),
            $checkoutCart->getMessagesBlock()->getSuccessMessages(),
            'Wrong success message is displayed.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift Card success apply message is present.';
    }
}
