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
 * Class AssertOrderPlacedSuccessMessage
 */
class AssertOrderPlacedSuccessMessage extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    const SUCCESS_MESSAGE = 'Thank you for your purchase!';

    /**
     * Assert that success message is correct
     *
     * @param CheckoutOnepageSuccess $checkoutOnepageSuccess
     * @return void
     */
    public function processAssert(CheckoutOnepageSuccess $checkoutOnepageSuccess)
    {
        $successMessage = $checkoutOnepageSuccess->getSuccessBlock()->getSuccessMessage();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $successMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_MESSAGE
            . "\nActual: " . $successMessage
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
