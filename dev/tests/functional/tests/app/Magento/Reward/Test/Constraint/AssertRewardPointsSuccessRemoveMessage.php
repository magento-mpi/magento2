<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Reward\Test\Constraint;

use Magento\Checkout\Test\Page\CheckoutCart;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertRewardPointsSuccessRemoveMessage
 * Assert that after remove reward points successful message appears
 */
class AssertRewardPointsSuccessRemoveMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'high';
    /* end tags */

    /**
     * Message displayed after remove reward points
     */
    const SUCCESS_REMOVE_MESSAGE = 'You removed the reward points from this order.';

    /**
     * Assert that after remove reward points successful message appears
     *
     * @param CheckoutCart $checkoutCart
     * @return void
     */
    public function processAssert(CheckoutCart $checkoutCart)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_REMOVE_MESSAGE,
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
        return 'Success message about removed reward points is present.';
    }
}
