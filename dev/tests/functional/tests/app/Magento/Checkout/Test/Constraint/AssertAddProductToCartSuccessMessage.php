<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Constraint;

use Magento\Checkout\Test\Page\CheckoutCart;
use Mtf\Constraint\AbstractConstraint;
use Mtf\Fixture\InjectableFixture;

/**
 * Class AssertAddProductToCartSuccessMessage
 * Assert that expected success message appeared on Shopping Cart page
 */
class AssertAddProductToCartSuccessMessage extends AbstractConstraint
{
    /**
     * Success add message
     */
    const SUCCESS_MESSAGE = 'You added %s to your shopping cart.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that expected success message appeared on Shopping Cart page
     *
     * @param CheckoutCart $checkoutCart
     * @param InjectableFixture $product
     * @return void
     */
    public function processAssert(CheckoutCart $checkoutCart, InjectableFixture $product)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            sprintf(self::SUCCESS_MESSAGE, $product->getName()),
            $checkoutCart->getMessagesBlock()->getSuccessMessages(),
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
        return 'Expected message appeared on shopping cart page.';
    }
}
