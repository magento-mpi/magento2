<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\AdvancedCheckout\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Checkout\Test\Page\CheckoutCart;

/**
 * Class AssertProductsAddBySkuSuccessMessage
 * Assert that after adding products by sku to shopping cart, successful message appears
 */
class AssertProductsAddBySkuSuccessMessage extends AbstractConstraint
{
    /**
     * Success adding products to shopping cart message
     */
    const SUCCESS_MESSAGE = 'You added %d %s to your shopping cart.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that success message is displayed after adding products by sku to shopping cart
     *
     * @param CheckoutCart $checkoutCart
     * @param array $products
     * @return void
     */
    public function processAssert(CheckoutCart $checkoutCart, array $products)
    {
        $productsQty = count($products);
        $qtyString = ($productsQty > 1) ? 'products' : 'product';
        \PHPUnit_Framework_Assert::assertEquals(
            sprintf(self::SUCCESS_MESSAGE, $productsQty, $qtyString),
            $checkoutCart->getMessagesBlock()->getSuccessMessages()
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Adding products by sku to shopping cart success message is present.';
    }
}
