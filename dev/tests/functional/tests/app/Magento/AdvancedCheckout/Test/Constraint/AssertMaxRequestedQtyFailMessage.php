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
 * Class AssertMaxRequestedQtyFailMessage
 * Assert that after adding products by sku to cart, requested quantity is more than allowed error message appears
 */
class AssertMaxRequestedQtyFailMessage extends AbstractConstraint
{
    /**
     * Error requested quantity message
     */
    const ERROR_QUANTITY_MESSAGE = 'The product cannot be added to cart in requested quantity.';

    /**
     * Error maximum quantity allowed message
     */
    const ERROR_MAXIMUM_QUANTITY_MESSAGE = 'The maximum quantity allowed for purchase is %d.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that requested quantity is more than allowed error message is displayed after adding products to cart
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
            \PHPUnit_Framework_Assert::assertContains(
                sprintf(self::ERROR_MAXIMUM_QUANTITY_MESSAGE, $product->getData('stock_data')['max_sale_qty']),
                $currentMessage,
                'Wrong error message is displayed.'
            );
        }
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Requested quantity is more than allowed error message is present after adding products to cart.';
    }
}
