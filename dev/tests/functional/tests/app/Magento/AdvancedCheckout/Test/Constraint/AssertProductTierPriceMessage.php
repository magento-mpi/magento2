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
 * Class AssertProductTierPriceMessage
 * Assert that product has tier price message appears after adding products by sku to shopping cart
 */
class AssertProductTierPriceMessage extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that product has tier price message appears after adding products by sku to shopping cart
     *
     * @param CheckoutCart $checkoutCart
     * @param array $requiredAttentionProducts
     * @return void
     */
    public function processAssert(CheckoutCart $checkoutCart, array $requiredAttentionProducts)
    {
        foreach ($requiredAttentionProducts as $product) {
            $messages = $checkoutCart->getAdvancedCheckoutCart()->getTierPriceMessages($product);
            $tierPrices = $product->getTierPrice();
            $productPrice = $product->getPrice();
            \PHPUnit_Framework_Assert::assertTrue(
                count($messages) !== count($tierPrices),
                'Wrong qty messages is displayed.'
            );
            foreach ($tierPrices as $key => $tierPrice) {
                $price = (bool)strpos($messages[$key], (string)$tierPrice['price']);
                $priceQty = (bool)strpos($messages[$key], (string)$tierPrice['price_qty']);
                $savePercent = (bool)strpos($messages[$key], $this->getSavePercent($productPrice, $tierPrice));
                \PHPUnit_Framework_Assert::assertTrue(
                    $price and $priceQty and $savePercent,
                    'Wrong message is displayed.'
                );
            }
        }
    }

    /**
     * Get save percent
     *
     * @param string $price
     * @param array $tierPrice
     * @return string
     */
    protected function getSavePercent($price, array $tierPrice)
    {
        $tierPriceQty = $tierPrice['price_qty'];

        return round(100 - ((100 * ($tierPrice['price'] * $tierPriceQty)) / ($price * $tierPriceQty))) . "%";
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Product has tier price message is present after adding products to cart.';
    }
}
