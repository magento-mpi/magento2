<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerBalance\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Checkout\Test\Page\CheckoutCart;

/**
 * Class AssertRemoveStoreCreditSuccessMessage
 * Assert that after remove store credit successful message appears
 */
class AssertRemoveStoreCreditSuccessMessage extends AbstractConstraint
{
    /**
     * Message displayed after remove store credit
     */
    const SUCCESS_REMOVE_MESSAGE = 'The store credit payment has been removed from shopping cart.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that after remove store credit successful message appears
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
        return 'Store credit success remove message is present.';
    }
}
