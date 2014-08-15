<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Constraint;

use Magento\Checkout\Test\Page\CheckoutOnepageSuccess;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertOrderSuccessPlacedMessage
 * Assert that success message is correct
 */
class AssertOrderSuccessPlacedMessage extends AbstractConstraint
{
    /**
     * Message of success checkout
     */
    const SUCCESS_MESSAGE = 'Thank you for your purchase!';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that success message is correct
     *
     * @param CheckoutOnepageSuccess $checkoutOnepageSuccess
     * @return void
     */
    public function processAssert(CheckoutOnepageSuccess $checkoutOnepageSuccess)
    {
        $successMessage = $checkoutOnepageSuccess->getTitleBlock()->getTitle();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $successMessage,
            'Wrong success message is displayed.'
        );
    }

    /**
     * Returns string representation of successful assertion
     *
     * @return string
     */
    public function toString()
    {
        return 'Success message on Checkout onepage success page is correct.';
    }
}
