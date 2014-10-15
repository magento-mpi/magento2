<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\AdvancedCheckout\Test\Constraint;

use Magento\Checkout\Test\Page\CheckoutCart;

/**
 * Class AssertMinRequestedQtyFailMessage
 * Assert that after adding products by sku to cart, requested quantity is less than allowed error message appears
 */
class AssertMinRequestedQtyFailMessage extends AbstractAssertErrorQuantityMessage
{
    /**
     * Error minimum quantity allowed message
     */
    const ERROR_MINIMUM_QUANTITY_MESSAGE = 'The minimum quantity allowed for purchase is %d.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that requested quantity is less than allowed error message is displayed after adding products to cart
     *
     * @param CheckoutCart $checkoutCart
     * @param array $requiredAttentionProducts
     * @return void
     */
    public function processAssert(CheckoutCart $checkoutCart, array $requiredAttentionProducts)
    {
        foreach ($requiredAttentionProducts as $product) {
            $currentMessage = $checkoutCart->getAdvancedCheckoutCart()->getFailedItemErrorMessage($product);
            \PHPUnit_Framework_Assert::assertContains(
                self::ERROR_QUANTITY_MESSAGE,
                $currentMessage,
                'Wrong error message is displayed.'
            );
            \PHPUnit_Framework_Assert::assertContains(
                sprintf(self::ERROR_MINIMUM_QUANTITY_MESSAGE, $product->getData('stock_data')['min_sale_qty']),
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
        return 'Requested quantity is less than allowed error message is present after adding products to cart.';
    }
}
