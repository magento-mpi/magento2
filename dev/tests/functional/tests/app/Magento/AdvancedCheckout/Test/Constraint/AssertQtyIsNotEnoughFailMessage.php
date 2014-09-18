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
 * Class AssertQtyIsNotEnoughFailMessage
 * Assert that after adding products by sku to shopping cart, requested quantity is not available error message appears
 */
class AssertQtyIsNotEnoughFailMessage extends AbstractConstraint
{
    /**
     * Requested quantity is not available error message
     */
    const ERROR_QUANTITY_MESSAGE = 'The requested quantity is not available.';

    /**
     * Quantity left in stock error message
     */
    const LEFT_IN_STOCK_ERROR_MESSAGE = 'Only %d left in stock';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that requested quantity is not available error message is displayed after adding products by sku to cart
     *
     * @param CheckoutCart $checkoutCart
     * @param array $products
     * @return void
     */
    public function processAssert(CheckoutCart $checkoutCart, $products)
    {
        foreach ($products as $product) {
            $currentMessage = $checkoutCart->getCartBlock()->getFailedItemErrorMessage($product);
            \PHPUnit_Framework_Assert::assertContains(
                self::ERROR_QUANTITY_MESSAGE,
                $currentMessage,
                'Wrong error message is displayed.'
            );
            $productQty = $product->getQuantityAndStockStatus()['qty'];
            \PHPUnit_Framework_Assert::assertContains(
                sprintf(self::LEFT_IN_STOCK_ERROR_MESSAGE, $productQty),
                $currentMessage,
                'Wrong error message is displayed.'
            );
        }
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Requested quantity is not available error message is present after adding products to shopping cart.';
    }
}
