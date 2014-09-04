<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Checkout\Test\Page\CheckoutCart;

/**
 * Class AssertRewardPointsRemoveSuccessMessage
 * Assert that after remove reward points successful message appears
 */
class AssertRewardPointsRemoveSuccessMessage extends AbstractConstraint
{
    /**
     * Message displayed after remove reward points
     */
    const SUCCESS_REMOVE_MESSAGE = 'You removed the reward points from this order.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

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
        return 'Reward points success remove message is present.';
    }
}
