<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Client\Browser;
use Mtf\Fixture\FixtureInterface;
use Mtf\Constraint\AbstractConstraint;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Catalog\Test\Page\Product\CatalogProductView;

/**
 * Class AssertProductInCart
 */
class AssertProductInCart extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assertion that the product is correctly displayed in cart
     *
     * @param CatalogProductView $catalogProductView
     * @param FixtureInterface $product
     * @param Browser $browser
     * @param CheckoutCart $checkoutCart
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        FixtureInterface $product,
        Browser $browser,
        CheckoutCart $checkoutCart
    ) {
        // Add product to cart
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $productOptions = $product->getCustomOptions();
        if ($productOptions) {
            $customOption = $catalogProductView->getCustomOptionsBlock();
            $options = $customOption->getOptions();
            $key = $productOptions[0]['title'];
            $customOption->selectProductCustomOption($options[$key]['title']);
        }
        $catalogProductView->getViewBlock()->clickAddToCart();

        // Check price
        $this->assertOnShoppingCart($product, $checkoutCart);
    }

    /**
     * Assert prices on the shopping cart
     *
     * @param FixtureInterface $product
     * @param CheckoutCart $checkoutCart
     * @return void
     */
    protected function assertOnShoppingCart(FixtureInterface $product, CheckoutCart $checkoutCart)
    {
        $cartBlock = $checkoutCart->getCartBlock();
        $productName = $product->getName();
        $productOptions = $product->getCustomOptions();
        $priceComparing = $product->getPrice();

        if ($groupPrice = $product->getGroupPrice()) {
            $groupPrice = reset($groupPrice);
            $priceComparing = $groupPrice['price'];
        }

        if ($specialPrice = $product->getSpecialPrice()) {
            $priceComparing = $specialPrice;
        }

        if (!empty($productOptions)) {
            $productOption = reset($productOptions);
            $optionsData = reset($productOption['options']);
            $optionName = $cartBlock->getCartItemOptionsNameByProductName($productName);
            $optionValue = $cartBlock->getCartItemOptionsValueByProductName($productName);

            \PHPUnit_Framework_Assert::assertTrue(
                trim($optionName) === $productOption['title']
                && trim($optionValue) === $optionsData['title'],
                'In the cart wrong option product.'
            );

            if ($optionsData['price_type'] === 'Percent') {
                $priceComparing = $priceComparing * (1 + $optionsData['price'] / 100);
            } else {
                $priceComparing += $optionsData['price'];
            }
        }

        $price = $checkoutCart->getCartBlock()->getProductPriceByName($productName);
        \PHPUnit_Framework_Assert::assertEquals(
            number_format($priceComparing, 2),
            $price,
            'Product price in shopping cart is not correct.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Product is correctly displayed in cart.';
    }
}
