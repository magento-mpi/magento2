<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\AdvancedCheckout\Test\Constraint;

use Magento\Checkout\Test\Page\CheckoutCart;
use Mtf\Constraint\AbstractConstraint;

/**
 * Assert that success message is present after requiring attention product has been deleted from cart.
 */
class AssertRequiringAttentionProductSuccessDeleteMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Success delete message.
     */
    const SUCCESS_MESSAGE = 'You removed the item.';

    /**
     * Assert that success message is present after requiring attention product has been deleted from cart.
     *
     * @param CheckoutCart $checkoutCart
     * @return void
     */
    public function processAssert(CheckoutCart $checkoutCart)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $checkoutCart->getMessagesBlock()->getSuccessMessages()
        );
    }

    /**
     * Returns string representation of successful assertion.
     *
     * @return string
     */
    public function toString()
    {
        return 'Success delete message is present on Shopping Cart page.';
    }
}
