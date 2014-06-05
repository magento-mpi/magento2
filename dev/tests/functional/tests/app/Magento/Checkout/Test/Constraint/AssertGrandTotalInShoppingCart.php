<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Constraint; 

use Mtf\Constraint\AbstractConstraint;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Checkout\Test\Fixture\Cart;

/**
 * Class AssertGrandTotalInShoppingCart
 */
class AssertGrandTotalInShoppingCart extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that grand total in the shopping cart is equals to expected total from data set
     *
     * @param CheckoutCart $checkoutCart
     * @param Cart $cart
     * @return void
     */
    public function processAssert(
        CheckoutCart $checkoutCart,
        Cart $cart
    ) {
        $cartGrandTotal = $checkoutCart->open()->getTotalsBlock()->getGrandTotal();
        $dataSetGrandTotal = number_format(($cart->getQty() * $cart->getPrice()), 2);
        \PHPUnit_Framework_Assert::assertEquals(
            $cartGrandTotal,
            $dataSetGrandTotal,
            'Shopping cart grand total: \'' . $cartGrandTotal
                . '\' not equals with total from data set: \'' . $dataSetGrandTotal . '\''
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Grand total in the shopping cart equals to expected total from data set.';
    }
}
