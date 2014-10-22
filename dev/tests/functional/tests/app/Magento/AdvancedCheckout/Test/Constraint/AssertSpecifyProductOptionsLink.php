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
 * Class AssertSpecifyProductOptionsLink
 * Assert that after adding products by sku to cart, specify the product's options link is displayed
 */
class AssertSpecifyProductOptionsLink extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that specify products options link is displayed after adding products to cart
     *
     * @param CheckoutCart $checkoutCart
     * @param array $requiredAttentionProducts
     * @return void
     */
    public function processAssert(CheckoutCart $checkoutCart, array $requiredAttentionProducts)
    {
        foreach ($requiredAttentionProducts as $product) {
            \PHPUnit_Framework_Assert::assertTrue(
                $checkoutCart->getAdvancedCheckoutCart()->specifyProductOptionsLinkIsVisible($product),
                "Specify the product's options link is not visible"
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
        return "Specify the product's options link is displayed after adding products to cart.";
    }
}
